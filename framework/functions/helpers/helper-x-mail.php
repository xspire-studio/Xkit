<?php
/**
 * A simple PHP wrapper class for sending email using the mail()\wp_mail() method.
 *
 * @package Xkit
 * @subpackage Xkit_Mail class
 *
 * EXAMPLE:
	$mail = new Xkit_Mail();
	$mail->setTo('youremail@gmail.com', 'Your Email')
		 ->setSubject('Test Message')
		 ->setFrom('no-reply@domain.com', 'Domain.com')
		 ->addMailHeader('Reply-To', 'no-reply@domain.com', 'Domain.com')
		 ->addMailHeader('Cc', 'bill@example.com', 'Bill Gates')
		 ->addMailHeader('Bcc', 'steve@example.com', 'Steve Jobs')
		 ->addAttachment('example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg')
		 ->addAttachment('example/lolcat_what.jpg')
		 ->setMessage('<strong>This is a test message.</strong>')
		 ->setWrap(100);
	$send = $mail->send();
 */


class Xkit_Mail{

	/*
	 * @var string $_content_type
	 */
	protected $_content_type = 'Content-Type: text/html; charset=UTF-8';

	/*
	 * @var int $_wrap
	 */
	protected $_wrap = 10000;

	/*
	 * @var array $_to
	 */
	protected $_to = array();

	/*
	 * @var string $_subject
	 */
	protected $_subject;

	/*
	 * @var string $_message
	 */
	protected $_message;

	/*
	 * @var array $_headers
	 */
	protected $_headers = array();

	/*
	 * @var string $_parameters
	 */
	protected $_params;

	/*
	 * @var array $_attachments
	 */
	protected $_attachments = array();

	/*
	 * @var string $_uid
	 */
	protected $_uid;

	/*
	 * @var string $_html_template
	 */
	protected $_html_template;


	/*
	 * __construct
	 *
	 * Resets the class properties.
	 */
	public function __construct()
	{
		$this->reset();
	}

	/*
	 * reset
	 *
	 * Resets all properties to initial state.
	 *
	 * @return Xkit_Mail
	 */
	public function reset()
	{
		$this->_content_type = 'Content-Type: text/html; charset=UTF-8';
		$this->_to = array();
		$this->_headers = array();
		$this->_subject = null;
		$this->_message = null;
		$this->_wrap = 10000;
		$this->_params = null;
		$this->_attachments = array();
		$this->_uid = $this->getUniqueId();
		$this->_html_template = $this->getDeafaultTemplate();

		return $this;
	}

	/*
	 * setTo
	 *
	 * @param string $email The email address to send to.
	 * @param string $name  The name of the person to send to.
	 *
	 * @return Xkit_Mail
	 */
	public function setTo( $email, $name = '' )
	{
		if( !$name ){
			$name = $email;
		}

		$this->_to[] = $this->formatHeader( (string) $email, (string) $name );
		return $this;
	}

	/*
	 * getTo
	 *
	 * Return an array of formatted To addresses.
	 *
	 * @return array
	 */
	public function getTo()
	{
		return $this->_to;
	}

	/*
	 * setSubject
	 *
	 * @param string $subject The email subject
	 *
	 * @return Xkit_Mail
	 */
	public function setSubject( $subject )
	{
		$this->_subject = $this->encodeUtf8(
			$this->filterOther( (string) $subject )
		);
		return $this;
	}

	/*
	 * getSubject function.
	 *
	 * @return string
	 */
	public function getSubject()
	{
		return $this->_subject;
	}

	/*
	 * setContentType
	 *
	 * @param string $content_type The content type to send.
	 *
	 * @return Xkit_Mail
	 */
	public function setContentType( $content_type )
	{
		$this->_content_type = $content_type;
		return $this;
	}

	/*
	 * setMessage
	 *
	 * @param string $message The message to send.
	 *
	 * @return Xkit_Mail
	 */
	public function setMessage( $message )
	{
		$this->_message = str_replace( "\n.", "\n..", (string) $message );

		if( strpos( $this->_html_template, '{%mail-content%}' ) !== false ){
			$this->_message = str_replace( '{%mail-content%}', $this->_message, $this->_html_template );
		}

		return $this;
	}

	/*
	 * setTemplate
	 *
	 * @param string $html_template The html template to send.
	 *
	 * @return Xkit_Mail
	 */
	public function setTemplate( $html_template )
	{
		$this->_html_template = $html_template;
		return $this;
	}

	/*
	 * getMessage
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return $this->_message;
	}

	/*
	 * addAttachment
	 *
	 * @param string $path     The file path to the attachment.
	 * @param string $filename The filename of the attachment when emailed.
	 *
	 * @return Xkit_Mail
	 */
	public function addAttachment( $path, $filename = null )
	{
		$filename = empty( $filename ) ? basename( $path ) : $filename;
		$this->_attachments[] = array(
			'path' => $path,
			'file' => $filename,
			'data' => $this->getAttachmentData( $path )
		);
		return $this;
	}

	/*
	 * getAttachmentData
	 *
	 * @param string $path The path to the attachment file.
	 *
	 * @return string
	 */
	public function getAttachmentData( $path )
	{
		$filesize = filesize( $path );
		$handle = xkit_fsopen( $path, "r" );
		$attachment = xkit_fsread( $handle, $filesize );
		xkit_fsclose( $handle );
		return chunk_split( xkit_encode_data64( $attachment ) );
	}

	/*
	 * setFrom
	 *
	 * @param string $email The email to send as from.
	 * @param string $name  The name to send as from.
	 *
	 * @return Xkit_Mail
	 */
	public function setFrom( $email, $name )
	{
		$this->addMailHeader( 'From', (string) $email, (string) $name );
		return $this;
	}

	/*
	 * addMailHeader
	 *
	 * @param string $header The header to add.
	 * @param string $email  The email to add.
	 * @param string $name   The name to add.
	 *
	 * @return Xkit_Mail
	 */
	public function addMailHeader( $header, $email = null, $name = null )
	{
		$address = $this->formatHeader( (string) $email, (string) $name );
		$this->_headers[] = sprintf( '%s: %s', (string) $header, $address );
		return $this;
	}

	/*
	 * addGenericHeader
	 *
	 * @param string $header The generic header to add.
	 * @param mixed  $value  The value of the header.
	 *
	 * @return Xkit_Mail
	 */
	public function addGenericHeader( $header, $value )
	{
		$this->_headers[] = sprintf(
			'%s: %s',
			(string) $header,
			(string) $value
		);
		return $this;
	}

	/*
	 * getHeaders
	 *
	 * Return the headers registered so far as an array.
	 *
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}

	/*
	 * setAdditionalParameters
	 *
	 * Such as "-fyouremail@yourserver.com
	 *
	 * @param string $additionalParameters The addition mail parameter.
	 *
	 * @return Xkit_Mail
	 */
	public function setParameters( $additionalParameters )
	{
		$this->_params = (string) $additionalParameters;
		return $this;
	}

	/**
	 * getAdditionalParameters
	 *
	 * @return string
	 */
	public function getParameters()
	{
		return $this->_params;
	}

	/**
	 * setWrap
	 *
	 * @param int $wrap The number of characters at which the message will wrap.
	 *
	 * @return Xkit_Mail
	 */
	public function setWrap( $wrap = 10000 )
	{
		$wrap = (int) $wrap;
		if ($wrap < 1) {
			$wrap = 10000;
		}
		$this->_wrap = $wrap;
		return $this;
	}

	/*
	 * getWrap
	 *
	 * @return int
	 */
	public function getWrap()
	{
		return $this->_wrap;
	}

	/*
	 * hasAttachments
	 * 
	 * Checks if the email has any registered attachments.
	 *
	 * @return bool
	 */
	public function hasAttachments()
	{
		return !empty( $this->_attachments );
	}

	/*
	 * assembleAttachment
	 *
	 * @return string
	 */
	public function assembleAttachmentHeaders()
	{
		$head = array();
		$head[] = "MIME-Version: 1.0";
		$head[] = "Content-Type: multipart/mixed; boundary=\"{$this->_uid}\"";

		return join( PHP_EOL, $head );
	}

	/*
	 * assembleAttachmentBody
	 *
	 * @return string
	 */
	public function assembleAttachmentBody()
	{
		$body = array();
		$body[] = "This is a multi-part message in MIME format.";
		$body[] = "--{$this->_uid}";
		$body[] = "Content-type:text/html; charset=\"utf-8\"";
		$body[] = "Content-Transfer-Encoding: 7bit";
		$body[] = "";
		$body[] = $this->_message;
		$body[] = "";
		$body[] = "--{$this->_uid}";

		foreach ( $this->_attachments as $attachment ) {
			$body[] = $this->getAttachmentMimeTemplate( $attachment );
		}

		return implode( PHP_EOL, $body );
	}

	/*
	 * getAttachmentMimeTemplate
	 *
	 * @param array  $attachment An array containing 'file' and 'data' keys.
	 * @param string $uid        A unique identifier for the boundary.
	 *
	 * @return string
	 */
	public function getAttachmentMimeTemplate( $attachment )
	{
		$file = $attachment['file'];
		$data = $attachment['data'];

		$head = array();
		$head[] = "Content-Type: application/octet-stream; name=\"{$file}\"";
		$head[] = "Content-Transfer-Encoding: base64";
		$head[] = "Content-Disposition: attachment; filename=\"{$file}\"";
		$head[] = "";
		$head[] = $data;
		$head[] = "";
		$head[] = "--{$this->_uid}";

		return implode( PHP_EOL, $head );
	}

	/*
	 * send
	 *
	 * @throws \RuntimeException on no 'To: ' address to send to.
	 * @return boolean
	 */
	public function send( $wp_mailer = false )
	{
		$to = $this->getToForSend();
		$headers = $this->getHeadersForSend();

		if ( empty($to) ) {
			throw new \RuntimeException(
				'Unable to send, no To address has been set.'
			);
		}

		if ( $this->hasAttachments() ) {
			$headers .= PHP_EOL . $this->assembleAttachmentHeaders();

			$message  = $this->assembleAttachmentBody();
		} else {
			$headers .= PHP_EOL . $this->_content_type;

			$message = $this->getWrapMessage();
		}

		if( $wp_mailer && function_exists( 'wp_mail' ) ){
			return wp_mail( $to, $this->_subject, $message, $headers, $this->_params );
		} else {
			return mail( $to, $this->_subject, $message, $headers, $this->_params );
		}
	}

	/*
	 * debug
	 *
	 * @return string
	 */
	public function debug()
	{
		return '<pre>' . print_r( $this, true ) . '</pre>';
	}

	/*
	 * magic __toString function
	 *
	 * @return string
	 */
	public function __toString()
	{
		return print_r( $this, true );
	}

	/*
	 * formatHeader
	 *
	 * Formats a display address for emails according to RFC2822 e.g.
	 * Name <address@domain.tld>
	 *
	 * @param string $email The email address.
	 * @param string $name  The display name.
	 *
	 * @return string
	 */
	public function formatHeader( $email, $name = null )
	{
		$email = $this->filterEmail( $email );
		if ( empty($name) ) {
			return $email;
		}
		$name = $this->encodeUtf8( $this->filterName( $name ) );
		return sprintf( '"%s" <%s>', $name, $email );
	}

	/*
	 * encodeUtf8
	 *
	 * @param string $value The value to encode.
	 *
	 * @return string
	 */
	public function encodeUtf8( $value )
	{
		$value = trim( $value );
		if ( preg_match( '/(\s)/', $value ) ) {
			return $this->encodeUtf8Words( $value );
		}
		return $this->encodeUtf8Word( $value );
	}

	/*
	 * encodeUtf8Word
	 *
	 * @param string $value The word to encode.
	 *
	 * @return string
	 */
	public function encodeUtf8Word( $value )
	{
		return sprintf( '=?UTF-8?B?%s?=', xkit_encode_data64( $value ) );
	}

	/*
	 * encodeUtf8Words
	 *
	 * @param string $value The words to encode.
	 *
	 * @return string
	 */
	public function encodeUtf8Words( $value )
	{
		$words = explode( ' ', $value );
		$encoded = array();
		foreach ( $words as $word ) {
			$encoded[] = $this->encodeUtf8Word( $word );
		}
		return join( $this->encodeUtf8Word(' '), $encoded );
	}

	/*
	 * filterEmail
	 *
	 * Removes any carriage return, line feed, tab, double quote, comma
	 * and angle bracket characters before sanitizing the email address.
	 *
	 * @param string $email The email to filter.
	 *
	 * @return string
	 */
	public function filterEmail( $email )
	{
		$rule = array(
			"\r" => '',
			"\n" => '',
			"\t" => '',
			'"'  => '',
			','  => '',
			'<'  => '',
			'>'  => ''
		);
		$email = strtr( $email, $rule );
		$email = filter_var( $email, FILTER_SANITIZE_EMAIL );
		return $email;
	}

	/*
	 * filterName
	 *
	 * Removes any carriage return, line feed or tab characters. Replaces
	 * double quotes with single quotes and angle brackets with square
	 * brackets, before sanitizing the string and stripping out html tags.
	 *
	 * @param string $name The name to filter.
	 *
	 * @return string
	 */
	public function filterName( $name )
	{
		$rule = array(
			"\r" => '',
			"\n" => '',
			"\t" => '',
			'"'  => "'",
			'<'  => '[',
			'>'  => ']',
		);
		$filtered = filter_var(
			$name,
			FILTER_SANITIZE_STRING,
			FILTER_FLAG_NO_ENCODE_QUOTES
		);
		return trim( strtr( $filtered, $rule ) );
	}

	/*
	 * filterOther
	 *
	 * Removes ASCII control characters including any carriage return, line
	 * feed or tab characters.
	 *
	 * @param string $data The data to filter.
	 *
	 * @return string
	 */
	public function filterOther( $data )
	{
		return filter_var( $data, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW );
	}

	/*
	 * getHeadersForSend
	 *
	 * @return string
	 */
	public function getHeadersForSend()
	{
		if ( empty( $this->_headers ) ) {
			return '';
		}
		return join( PHP_EOL, $this->_headers );
	}

	/*
	 * getToForSend
	 *
	 * @return string
	 */
	public function getToForSend()
	{
		if ( empty( $this->_to ) ) {
			return '';
		}
		return join( ', ', $this->_to );
	}

	/*
	 * getUniqueId
	 *
	 * @return string
	 */
	public function getUniqueId()
	{
		return md5( uniqid( time() ) );
	}

	/*
	 * getWrapMessage
	 *
	 * @return string
	 */
	public function getWrapMessage()
	{
		return wordwrap( $this->_message, $this->_wrap );
	}

	/*
	 * getDeafaultTemplate
	 *
	 * @return string
	 */
	public function getDeafaultTemplate()
	{
		ob_start();
		?>
			<html>
				<head>
					<style>
						html, body{
							background: #F7F7F7;
							width: 100%;
							height: 100%;
							margin: 0px;
							padding: 0px;
						}
						*{
							font: normal 14px/20px Arial;
							color: #343434;
						}

						.mail-content{
							border-top: 5px solid #DF2626;
							margin: 10px;
							padding: 20px;
							background-color: #FFF;

							border-radius: 8px;
							-webkit-border-radius: 8px;
							-moz-border-radius: 8px;
						}
						.mail-content .before-text,
						.mail-content .after-text{
							margin: 10px 0;
						}
						.mail-content h1{
							margin-top: 15px;
							margin-bottom: 15px;
							font: 600 18px/16px Arial;
							color: #DF2626;
							text-align: left;
							text-transform: uppercase;
						}
						.mail-content strong{
							font-weight: bold;
						}
						.mail-content table tr td {
							padding: 10px 14px;
						}
					</style>
				</head>
				<body>
					<div class="mail-content">
						{%mail-content%}
					</div>
				</body>
			</html>
		<?php
		return ob_get_clean();
	}

	/*
	 * tableBuilder
	 *
	 * @return string
	 */
	public function tableBuilder( $table_args = array() )
	{
		if( is_array( $table_args ) && $table_args ){
			$table_container = '';

			foreach( $table_args as $label => $item ){
				$table_container .= "<tr><td><strong>{$label}</strong></td><td>{$item}</td></tr>";
			}

			if( $table_container ){
				$table_container  = "<table>{$table_container}</table>";
			}
		}

		return $table_container;
	}
}
?>