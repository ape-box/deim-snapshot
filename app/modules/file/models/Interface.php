<?php

interface file_models_Interface
{
	/**
	 * @return string file name without extension
	 */
	public function getName();

	/**
	 * @return string file extension withot dot (example: "jpg" instead of ".jpg")
	 */
	public function getExt();

	/**
	 * @return string file name with extension
	 */
	public function getFullName();

	/**
	 * @return string file's mime type
	 */
	public function getMime();

	/**
	 * @return file's modification time timestamp
	 */
	public function getMtime();

	/**
	 * @return string file content
	 */
	public function getContent();

	/**
	 * @return boolean
	 */
	public function isImage();

	/**
	 * @param integer $w Larghezza dell'immagine output
	 * @param integer $h Altezza dell'immagine in output
	 * @param string $mime the mime desired for the resulting image
	 * @param bool $watermark
	 * @param bool $crop Indica se dobbiamo centrare e tagliare l'immagine
	 * @return string an image content edited as requested
	 */
	public function editImage($w, $h);
}