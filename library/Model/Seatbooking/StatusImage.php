<?php

    class Model_Seatbooking_StatusImage {
        public $seatbooking;
        public $cache_directory;
        
        function __construct(Model_Seatbooking $seatbooking, $cache_directory){
            $this->seatbooking = $seatbooking;
            $this->filename_xmlconfig = "$seatbooking->config_directory/statusimage/coordinates.xml";
            $this->filename_bg        = "{$this->seatbooking->config_directory}/statusimage/floorplan.png";
            $this->filename_marker    = "{$this->seatbooking->config_directory}/statusimage/unavailable_marker.png";
            $this->filename_image     = "{$cache_directory}/seatbook_status_cached.png";
            $this->filename_meta      = "{$cache_directory}/seatbook_status_cached_meta.txt";
        }

        function xmlToArray(DOMDocument $doc){
            $coords = array();
            foreach($doc->firstChild->childNodes as $node){
                if($node instanceof DOMElement){
                    $id = $node->getAttribute('id');
                    $x  = $node->getAttribute('x' );
                    $y  = $node->getAttribute('y' );
                    $coords[$id] = array($x, $y);
                }
            }
            return $coords;
        }
        function load_xmlconfig_to_array(){
            $doc = new DOMDocument();
            $doc->load($this->filename_xmlconfig);
            return $this->xmlToArray($doc);
        }

        private function generate(){
            $taken_seats_result = $this->seatbooking->get_taken_seats_ids();
            $taken_seats = array();
            foreach($taken_seats_result as $seat)
                $taken_seats[$seat] = 0;

            $coords = $this->load_xmlconfig_to_array();

            $taken_seats_coords = array_intersect_key($coords, $taken_seats);

            $bg = new imagick($this->filename_bg);
            $marker = new imagick($this->filename_marker);

            $marker_width  = $marker->getImageWidth();
            $marker_height = $marker->getImageHeight();
            
            foreach($taken_seats_coords as $seat){
                $x = $seat[0] - floor($marker_width / 2);
                $y = $seat[1] - floor($marker_width / 2);
                $bg->compositeImage($marker, imagick::COMPOSITE_OVER, $x, $y);
            }

            return $bg;
        }

        private function save(Imagick $image, $checksum){
            $file_image = fopen($this->filename_image, 'ab');
            $file_meta  = fopen($this->filename_meta , 'ab');
            flock($file_image, LOCK_EX);
            flock($file_meta , LOCK_EX);
            ftruncate($file_image, 0);
            ftruncate($file_meta , 0);
            fwrite($file_image, (string)$image   );
            fwrite($file_meta ,         $checksum);
            fclose($file_image);
            fclose($file_meta );
        }

        private function load(){
            $file_image     = fopen($this->filename_image, 'rb');
            $file_meta      = fopen($this->filename_meta , 'rb');
            flock($file_image, LOCK_SH);
            flock($file_meta , LOCK_SH);
            return array($file_image, $file_meta);
        }

        public function get(){
            $file_exists_image = file_exists($this->filename_image);
            $file_exists_meta  = file_exists($this->filename_meta );

            if(!$file_exists_image || !$file_exists_meta){
                touch($this->filename_image);
                touch($this->filename_meta);
            }
            while(true){
                list($file_image, $file_meta) = $this->load();
                $checksum_current = (string)$this->seatbooking->get_content_checksum();
                $checksum_cache   = (string)stream_get_contents($file_meta);
                $cache_needs_update = $checksum_current !== $checksum_cache;
                if($cache_needs_update){
                    fclose($file_image);
                    fclose($file_meta);
                    $this->save($this->generate(), $checksum_current);
                    continue;
                }
                return $file_image;
            }
        }

    }