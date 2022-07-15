<?php

class CrwdfndLog {
	private $error;
	private $warn;
	private $notice;
	private static $intance;
	private static $log_file;
	private static $log_auth_file;
	private function __construct() {
		$this->error  = array();
		$this->warn   = array();
		$this->notice = array();
	}
	private static function gen_log_file_names() {
		if ( ! empty( self::$log_file ) && ! empty( self::$log_auth_file ) ) {
			return;
		}
		$settings = CrwdfndSettings::get_instance();
		$suffix   = $settings->get_value( 'log-file-suffix' );
		if ( empty( $suffix ) ) {
			$suffix = uniqid();
			$settings->set_value( 'log-file-suffix', $suffix );
			$settings->save();
		}
		self::$log_file      = "log-{$suffix}.txt";
		self::$log_auth_file = "log-auth-{$suffix}.txt";
	}

	public static function output_log( $type = 'd' ) {
		if ( 'd' !== $type && 'a' !== $type ) {
			return;
		}
		self::gen_log_file_names();
		$log_file = 'd' === $type ? self::$log_file : self::$log_auth_file;

		$fp = fopen( CROWDFUND_ME_PATH . $log_file, 'r' );
		header( 'Content-Type: text/plain' );
		fpassthru( $fp );
		die;
	}

	public static function get_logger( $context = '' ) {
		$context = empty( $context ) ? 'default' : $context;
		if ( ! isset( self::$intance[ $context ] ) ) {
			self::$intance[ $context ] = new CrwdfndLog();
		}
		return self::$intance[ $context ];
	}
	public function error( $msg ) {
		$this->error[] = $msg;
	}
	public function warn( $msg ) {
		$this->warn[] = $msg;
	}
	public function debug( $msg ) {
		$this->notice[] = $msg;
	}
	public function get( $to_screen = false ) {
		$msg = '';
		foreach ( $this->error as $error ) {
			$msg .= 'ERROR: ' . $error . ( $to_screen ? '<br/>' : "\n" );
		}
		foreach ( $this->warn as $warn ) {
			$msg .= 'WARN: ' . $warn . ( $to_screen ? '<br/>' : "\n" );
		}
		foreach ( $this->notice as $notice ) {
			$msg = 'NOTICE: ' . $notice . ( $to_screen ? '<br/>' : "\n" );
		}
		return $msg;
	}
	public static function writeall( $path = '' ) {
		if ( empty( $path ) ) {
			self::gen_log_file_names();
			$path = CROWDFUND_ME_PATH . self::$log_file;}
		$fp   = fopen( $path, 'a' );
		$date = current_time( 'mysql' );
		fwrite( $fp, strtoupper( $date ) . ":\n" );
		fwrite( $fp, str_repeat( '-=', ( strlen( $date ) + 1.0 ) / 2.0 ) . "\n" );
		foreach ( self::$intance as $context => $intance ) {
			fwrite( $fp, strtoupper( $context ) . ":\n" );
			fwrite( $fp, str_repeat( '=', strlen( $context ) + 1 ) . "\n" );
			fwrite( $fp, $intance->get() );
		}
		fclose( $fp );
	}

	public static function log_simple_debug( $message, $success, $end = false ) {
		$settings      = CrwdfndSettings::get_instance();
		$debug_enabled = $settings->get_value( 'enable-debug' );
		if ( empty( $debug_enabled ) ) {//Debug is not enabled
			return;
		}

		//Lets write to the log file
		self::gen_log_file_names();
		$debug_log_file_name = CROWDFUND_ME_PATH . self::$log_file;

		// Timestamp
		$log_timestamp = CrwdfndUtils::get_current_timestamp_for_debug_log();
		$text          = '[' . $log_timestamp . '] - ' . ( ( $success ) ? 'SUCCESS: ' : 'FAILURE: ' ) . $message . "\n";
		if ( $end ) {
			$text .= "\n------------------------------------------------------------------\n\n";
		}
		// Write to log
		$fp = fopen( $debug_log_file_name, 'a' );
		fwrite( $fp, $text );
		fclose( $fp );  // close file
	}

	public static function log_array_data_to_debug( $array_to_write, $success, $end = false ) {
		$settings      = CrwdfndSettings::get_instance();
		$debug_enabled = $settings->get_value( 'enable-debug' );
		if ( empty( $debug_enabled ) ) {//Debug is not enabled
			return;
		}

		//Lets write to the log file
		self::gen_log_file_names();
		$debug_log_file_name = CROWDFUND_ME_PATH . self::$log_file;

		// Timestamp
		$log_timestamp = CrwdfndUtils::get_current_timestamp_for_debug_log();
		$text          = '[' . $log_timestamp . '] - ' . ( ( $success ) ? 'SUCCESS: ' : 'FAILURE: ' ) . "\n";
		ob_start();
		print_r( $array_to_write );
		$var = ob_get_contents();
		ob_end_clean();
		$text .= $var;

		if ( $end ) {
			$text .= "\n------------------------------------------------------------------\n\n";
		}
		// Write to log
		$fp = fopen( $debug_log_file_name, 'a' );
		fwrite( $fp, $text );
		fclose( $fp );  // close file
	}

	public static function log_auth_debug( $message, $success, $end = false ) {
		$settings      = CrwdfndSettings::get_instance();
		$debug_enabled = $settings->get_value( 'enable-debug' );
		if ( empty( $debug_enabled ) ) {//Debug is not enabled
			return;
		}

		//Lets write to the log file
		self::gen_log_file_names();
		$debug_log_file_name = CROWDFUND_ME_PATH . self::$log_auth_file;

		// Timestamp
		$log_timestamp = CrwdfndUtils::get_current_timestamp_for_debug_log();
		$text          = '[' . $log_timestamp . '] - ' . ( ( $success ) ? 'SUCCESS: ' : 'FAILURE: ' ) . $message . "\n";
		if ( $end ) {
			$text .= "\n------------------------------------------------------------------\n\n";
		}
		// Write to log
		$fp = fopen( $debug_log_file_name, 'a' );
		fwrite( $fp, $text );
		fclose( $fp );  // close file
	}

	public static function reset_swmp_log_files() {
		$log_reset = true;
		self::gen_log_file_names();
		$logfile_list = array(
			CROWDFUND_ME_PATH . self::$log_file,
			CROWDFUND_ME_PATH . self::$log_auth_file,
		);

		foreach ( $logfile_list as $logfile ) {
			if ( empty( $logfile ) ) {
				continue;
			}

			$log_timestamp = CrwdfndUtils::get_current_timestamp_for_debug_log();
			$text          = '[' . $log_timestamp . '] - SUCCESS: Log file reset';
			$text         .= "\n------------------------------------------------------------------\n\n";
			$fp            = fopen( $logfile, 'w' );
			if ( $fp != false ) {
				@fwrite( $fp, $text );
				@fclose( $fp );
			} else {
				$log_reset = false;
			}
		}
		return $log_reset;
	}

}
