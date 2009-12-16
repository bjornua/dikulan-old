<?php

    class Model_Seatbooking_StatusImage {
        public $seatbooking;
        public $cache_directory;
        
        function __construct(Model_Seatbooking $seatbooking, $cache_directory){
            $this->seatbooking = $seatbooking;
            $this->cache_directory = $cache_directory;
            $this->xmlconfig_filename = "$seatbooking->config_directory/statusimage/coordinates.xml";
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
            $doc->load($this->xmlconfig_filename);
            return $this->xmlToArray($doc);
        }

        private function generate(){
            $bg_filename = $this->seatbooking->config_directory . '/statusimage/floorplan.png';
            $marker_filename = $this->seatbooking->config_directory . '/statusimage/unavailable_marker.png';
            $coord_filename = $this->seatbooking->config_directory . '/statusimage/coordinates.xml';

            $taken_seats_result = $this->seatbooking->get_taken_seats_ids();
            $taken_seats = array();
            foreach($taken_seats_result as $seat)
                $taken_seats[$seat] = 0;

            $coords = $this->load_xmlconfig_to_array();

            $taken_seats_coords = array_intersect_key($coords, $taken_seats);

            $bg = new imagick($bg_filename);
            $marker = new imagick($marker_filename);

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
            $filename_image = "{$this->cache_directory}/seatbook_status_cached.png";
            $filename_meta  = "{$this->cache_directory}/seatbook_status_cached_meta.txt";
            $file_image = fopen($filename_image, 'ab');
            $file_meta  = fopen($filename_meta , 'ab');
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
            $filename_image = "{$this->cache_directory}/seatbook_status_cached.png";
            $filename_meta  = "{$this->cache_directory}/seatbook_status_cached_meta.txt";
            $file_image     = fopen($filename_image, 'rb');
            $file_meta      = fopen($filename_meta , 'rb');
            flock($file_image, LOCK_SH);
            flock($file_meta, LOCK_SH);
            return array($file_image, $file_meta);
        }

        public function get(){
            list($file_image, $file_checksum) = $this->load();
            $current_checksum = $this->seatbooking->get_content_checksum();
            $cached_checksum = stream_get_contents($file_checksum);

            if($current_checksum != $cached_checksum){
                fclose($file_image);
                fclose($file_checksum);
                $this->save($this->generate(), $current_checksum);
                return $this->get();
            }
            return $file_image;
            
        }

    }