<?php

class SeatBookingController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {

    }
    public function availableSeatsAction()
    {
        $db = $this->getInvokeArg('bootstrap')->getResource('db');

        $tablename = 'plads';
        $config_directory = APPLICATION_PATH . '/configs/seatbooking';

        $seatbooking = new Model_Seatbooking($config_directory, $db, $tablename);
        $cache_directory = APPLICATION_PATH . '/data/seatbooking';
        $status_image = new Model_Seatbooking_StatusImage($seatbooking, $cache_directory);
        $image_file_pointer = $status_image->get();

        $this->_helper->layout->disableLayout();
        $this->view->image = $image_file_pointer;
    }


}

