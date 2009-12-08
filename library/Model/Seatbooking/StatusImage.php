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

            $bg = imagecreatefrompng($bg_filename);
            $marker = imagecreatefrompng($marker_filename);

            $marker_width  = imagesx($marker);
            $marker_height = imagesy($marker);
            
            foreach($taken_seats_coords as $seat){
                $x = $seat[0] - floor($marker_width / 2);
                $y = $seat[1] - floor($marker_width / 2);
                imagecopy($bg, $marker, $x, $y, 0, 0, $marker_width, $marker_height);
            }

            return $bg;
        }

        private function save($image, $checksum){
            $image_filename = $this->cache_directory .'/seatbook_status_cached.png';
            $image_meta_filename = $this->cache_directory .'/seatbook_status_cached_meta.txt';
            
            imagepng($image, $image_filename, 9);
            file_put_contents($image_meta_filename, $checksum);
        }

        private function load(){
            $image_filename = $this->cache_directory .'/seatbook_status_cached.png';
            return fopen($image_filename,'r');
        }

        public function get(){
            $image_meta_filename = $this->cache_directory .'/seatbook_status_cached_meta.txt';
            $checksum = file_get_contents($image_meta_filename);
            $current_checksum = $this->seatbooking->get_content_checksum();

            if($current_checksum != $checksum)
                $this->save($this->generate(), $current_checksum);
            
            return $this->load();
            
        }

    }