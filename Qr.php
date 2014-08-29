<?php
/**
 * Created by PhpStorm.
 * User: gsch
 * Date: 29/08/2014
 * Time: 09:47
 */

namespace Negko\QRBundle;


use Negko\QRBundle\Exception\InvalidArgumentException;

class Qr {

    const API_CHART_URL = "https://chart.googleapis.com/chart";

    private $_max_size = 500;

    private $_size = null;

    private $qr = null;

    private $_data;

    /**
     *
     */
    function __construct()
    {
        $this->setSize($this->_max_size);
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param null $size
     */
    public function setSize($size)
    {
        if ( $size > $this->_max_size ){
            throw new InvalidArgumentException('Size cannot exceed '.$this->_max_size.', ' . $size . ' used');
        }

        $this->_size = $size;
    }

    /**
     * @return null
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * @param null $qr
     */
    public function setQr($qr)
    {
        $this->qr = $qr;
    }

    /**
     * @return null
     */
    public function getQr()
    {
        return $this->qr;
    }

    public function initQr()
    {
        $this->qr = imagecreatefrompng( $this->getQRUrl() );
    }

    public function getQr(){
        if ( !$this->qr )
            $this->initQr();

        return $this->qr;
    }

    public function addLogo( $logo_path ){
        $logo = imagecreatefromstring( file_get_contents( $logo_path ) );

        $QR_width = imagesx($this->getQr());
        $QR_height = imagesy($this->getQr());
        $logo_width = imagesx($logo);
        $logo_height = imagesy($logo);

        $logo_qr_width = $QR_width/1.5;
        $scale = $logo_width/$logo_qr_width;
        $logo_qr_height = $logo_height/$scale;

        imagecopyresampled($this->qr, $logo, ($QR_width-$logo_qr_width)/2 , ($QR_height-$logo_qr_height)/2 , 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);

    }

    private function getQRUrl()
    {
        return self::API_CHART_URL . '?cht=qr&chld=H|1&chs='. $this->getSize() .'&chl='. $this->getData();
    }

    /**
     * Bookmark code
     *
     * @param string $title
     * @param string $url
     */
    public function bookmark($title = null, $url = null) {
        $this->setData("MEBKM:TITLE:{$title};URL:{$url};;");
    }

    /**
     * MECARD code
     *
     * @param string $name
     * @param string $address
     * @param string $phone
     * @param string $email
     */
    public function contact($name = null, $address = null, $phone = null, $email = null) {
        $this->setData("MECARD:N:{$name};ADR:{$address};TEL:{$phone};EMAIL:{$email};;");
    }

    /**
     * Create code with GIF, JPG, etc.
     *
     * @param string $type
     * @param string $size
     * @param string $content
     */
    public function content($type = null, $size = null, $content = null) {
        $this->setData("CNTS:TYPE:{$type};LNG:{$size};BODY:{$content};;");
    }


    /**
     * Email address code
     *
     * @param string $email
     * @param string $subject
     * @param string $message
     */
    public function email($email = null, $subject = null, $message = null) {
        $this->setData("MATMSG:TO:{$email};SUB:{$subject};BODY:{$message};;");
    }

    /**
     * Geo location code
     *
     * @param string $lat
     * @param string $lon
     * @param string $height
     */
    public function geo($lat = null, $lon = null, $height = null) {
        $this->setData($this->_data = "GEO:{$lat},{$lon},{$height}");
    }

    /**
     * Telephone number code
     *
     * @param string $phone
     */
    public function phone($phone = null) {
        $this->setData($this->_data = "TEL:{$phone}");
    }

    /**
     * SMS code
     *
     * @param string $phone
     * @param string $text
     */
    public function sms($phone = null, $text = null) {
        $this->setData($this->_data = "SMSTO:{$phone}:{$text}");
    }

    /**
     * Text code
     *
     * @param string $text
     */
    public function text($text = null) {
        $this->setData($text);
    }

    /**
     * URL code
     *
     * @param string $url
     */
    public function url($url = null) {
        $this->setData($this->_data = preg_match("#^https?\:\/\/#", $url) ? $url : "http://{$url}");
    }

    /**
     * Wifi code
     *
     * @param string $type
     * @param string $ssid
     * @param string $password
     */
    public function wifi($type = null, $ssid = null, $password = null) {
        $this->setData("WIFI:T:{$type};S{$ssid};{$password};;");
    }


} 