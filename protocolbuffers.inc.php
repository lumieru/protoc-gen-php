<?php

/**
	Constants
 */
define('EXP_CODE_NOT_IMPLEMENTED', 0x8f000001);
define('EXP_CODE_READ_ERROR', 0x8f000002);
define('EXP_CODE_WRITE_ERROR', 0x8f000003);
define('EXP_CODE_SEEK_ERROR', 0x8f000004);
define('EXP_CODE_UNSUPPORTED_WIRE_TYPE', 0x8f000005);
define('EXP_CODE_INVALID_PARAMETER', 0x8f000006);
define('EXP_CODE_MISS_REQUIRED_FIELDS', 0x8f000007);
	
class ProtobufEnum {

	public static function toString($value) {
		if (is_null($value))
			return null;
		if (array_key_exists($value, self::$_values))
			return self::$_values[$value];
		return 'UNKNOWN';
	}
}

class ProtobufMessage {

	function __construct($fp = NULL, &$limit = PHP_INT_MAX) {
		if($fp !== NULL) {
			if (is_string($fp)) {
				// If the input is a string, turn it into a stream and decode it
				$str = $fp;
				$fp = fopen('php://memory', 'r+b');
				fwrite($fp, $str);
				rewind($fp);
			}
			$this->read($fp, $limit);
			if (isset($str))
				fclose($fp);
		}
	}
}	
	
/**
 * Class to aid in the parsing and creating of Protocol Buffer Messages
 * This class should be included by the developer before they use a
 * generated protobuf class.
 *
 * @author Andrew Brampton
 *
 */
class Protobuf {

	const TYPE_DOUBLE   = 1;   // double, exactly eight bytes on the wire.
	const TYPE_FLOAT    = 2;   // float, exactly four bytes on the wire.
	const TYPE_INT64    = 3;   // int64, varint on the wire.  Negative numbers
                               // take 10 bytes.  Use TYPE_SINT64 if negative
                               // values are likely.
	const TYPE_UINT64   = 4;   // uint64, varint on the wire.
	const TYPE_INT32    = 5;   // int32, varint on the wire.  Negative numbers
                               // take 10 bytes.  Use TYPE_SINT32 if negative
                               // values are likely.
	const TYPE_FIXED64  = 6;   // uint64, exactly eight bytes on the wire.
	const TYPE_FIXED32  = 7;   // uint32, exactly four bytes on the wire.
	const TYPE_BOOL     = 8;   // bool, varint on the wire.
	const TYPE_STRING   = 9;   // UTF-8 text.
	const TYPE_GROUP    = 10;  // Tag-delimited message.  Deprecated.
	const TYPE_MESSAGE  = 11;  // Length-delimited message.

	const TYPE_BYTES    = 12;  // Arbitrary byte array.
	const TYPE_UINT32   = 13;  // uint32, varint on the wire
	const TYPE_ENUM     = 14;  // Enum, varint on the wire
	const TYPE_SFIXED32 = 15;  // int32, exactly four bytes on the wire
	const TYPE_SFIXED64 = 16;  // int64, exactly eight bytes on the wire
	const TYPE_SINT32   = 17;  // int32, ZigZag-encoded varint on the wire
	const TYPE_SINT64   = 18;  // int64, ZigZag-encoded varint on the wire

	/**
	 * Returns a string representing this wiretype
	 */
	public static function get_wiretype($wire_type) {
		switch ($wire_type) {
			case 0: return 'varint';
			case 1: return '64-bit';
			case 2: return 'length-delimited';
			case 3: return 'group start';
			case 4: return 'group end';
			case 5: return '32-bit';
			default: return 'unknown';
		}
	}

	/**
	 * Returns how big (in bytes) this number would be as a varint
	 */
	public static function size_varint($i) {
/*		$len = 0;
		do {
			$i = $i >> 7;
			$len++;
		} while ($i != 0);
		return $len;
*/
		// TODO Change to a binary search
		if ($i < 0x80)
			return 1;
		if ($i < 0x4000)
			return 2;
		if ($i < 0x200000)
			return 3;
		if ($i < 0x10000000)
			return 4;
		if ($i < 0x800000000)
			return 5;
		if ($i < 0x40000000000)
			return 6;
		if ($i < 0x2000000000000)
			return 7;
		if ($i < 0x100000000000000)
			return 8;
		if ($i < 0x8000000000000000)
			return 9;
	}

	/**
	 * Tries to read a varint from $fp.
	 * @returns the Varint from the stream, or false if the stream has reached eof.
	 */
	public static function read_varint($fp, &$limit = null) {
		$value = '';
		$len = 0;
		do { // Keep reading until we find the last byte
			$b = fread($fp, 1);
			if ($b === false)
				throw new Exception(__METHOD__, EXP_CODE_READ_ERROR);
			if (strlen($b) < 1)
				break;

			$value .= $b;
			$len++;
		} while ($b >= "\x80");

		if ($len == 0) {
		/**
			if (feof($fp))
				return false;
		 */
			throw new Exception(__METHOD__, EXP_CODE_READ_ERROR);
		}

		if ($limit !== null)
			$limit -= $len;

		$i = 0;
		$shift = 0;
		for ($j = 0; $j < $len; $j++) {
			$i |= ((ord($value[$j]) & 0x7F) << $shift);
			$shift += 7;
		}

		return $i;
	}

	public static function read_double($fp){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function read_float ($fp){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function read_uint64($fp){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function read_int64 ($fp){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function read_uint32($fp){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function read_int32 ($fp){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function read_zint32($fp){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function read_zint64($fp){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	
	/**
	 * Read string or bytes (since strlen is safe for binary data)
	 */
	public static function read_string ($fp, &$limit = null){
		
		$len = Protobuf::read_varint($fp, $limit);
		if ($len === false)
            throw new Exception(__METHOD__, EXP_CODE_READ_ERROR);
		
		if ($len > 0){
            $tmp = fread($fp, $len);
			if ($tmp === false){
				throw new Exception(__METHOD__, EXP_CODE_READ_ERROR);
			}
		}
		else
            $tmp = '';
		
		$limit-=$len;
		
		return $tmp;
		
	}
	
	/**
	 * Writes a varint to $fp
	 * returns the number of bytes written
	 * @param $fp
	 * @param $i The int to encode
	 * @return The number of bytes written
	 */
	public static function write_varint($fp, $i) {
		$len = 0;
		do {
			$v = $i & 0x7F;
			$i = $i >> 7;

			if ($i != 0)
				$v |= 0x80;

			if (fwrite($fp, chr($v)) !== 1)
				throw new Exception(__METHOD__, EXP_CODE_WRITE_ERROR);

			$len++;
		} while ($i != 0);

		return $len;
	}

	public static function write_double($fp, $d){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function write_float ($fp, $f){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function write_uint64($fp, $i){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function write_int64 ($fp, $i){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function write_uint32($fp, $i){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function write_int32 ($fp, $i){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function write_zint32($fp, $i){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	public static function write_zint64($fp, $i){throw new Exception(__METHOD__, EXP_CODE_NOT_IMPLEMENTED);}
	
	/**
	 * Write string or bytes (since strlen is safe for binary data)
	 */
	public static function write_string ($fp, $s){
		
		$len = strlen($s);
		Protobuf::write_varint($fp, $len);
		if( fwrite($fp, $s) !== $len )
			throw new Exception(__METHOD__, EXP_CODE_WRITE_ERROR);
		
		return $len;
		
	}
	
	/**
	 * Seek past a varint
	 */
	public static function skip_varint($fp) {
		$len = 0;
		do { // Keep reading until we find the last byte
			$b = fread($fp, 1);
			if ($b === false)
				throw new Exception(__METHOD__, EXP_CODE_READ_ERROR);
			$len++;
		} while ($b >= "\x80");
		return $len;
	}

	/**
	 * Seek past the current field
	 */
	public static function skip_field($fp, $wire_type) {
		switch ($wire_type) {
			case 0: // varint
				return Protobuf::skip_varint($fp);

			case 1: // 64bit
				if (fseek($fp, 8, SEEK_CUR) === -1)
					throw new Exception(__METHOD__ . ProtoBuf::get_wiretype(1), EXP_CODE_SEEK_ERROR);
				return 8;

			case 2: // length delimited
				$varlen = 0;
				$len = Protobuf::read_varint($fp, $varlen);
				if (fseek($fp, $len, SEEK_CUR) === -1)
					throw new Exception(__METHOD__ . ProtoBuf::get_wiretype(2), EXP_CODE_SEEK_ERROR);
				return $len - $varlen;

			//case 3: // Start group TODO we must keep looping until we find the closing end grou

			//case 4: // End group - We should never skip a end group!
			//	return 0; // Do nothing

			case 5: // 32bit
				if (fseek($fp, 4, SEEK_CUR) === -1)
					throw new Exception(__METHOD__ . ProtoBuf::get_wiretype(5), EXP_CODE_SEEK_ERROR);
				return 4;

			default:
				throw new Exception(__METHOD__ . ProtoBuf::get_wiretype($wire_type), EXP_CODE_UNSUPPORTED_WIRE_TYPE);
		}
	}

	/**
	 * Read a unknown field from the stream and return its raw bytes
	 */
	public static function read_field($fp, $wire_type, &$limit = null) {
		switch ($wire_type) {
			case 0: // varint
				return Protobuf::read_varint($fp, $limit);

			case 1: // 64bit
				$limit -= 8;
				return fread($fp, 8);

			case 2: // length delimited
				$len = Protobuf::read_varint($fp, $limit);
				$limit -= $len;
				return fread($fp, $len);

			//case 3: // Start group TODO we must keep looping until we find the closing end grou

			//case 4: // End group - We should never skip a end group!
			//	return 0; // Do nothing

			case 5: // 32bit
				$limit -= 4;
				return fread($fp, 4);

			default:
				throw new Exception(__METHOD__ . ProtoBuf::get_wiretype($wire_type), EXP_CODE_UNSUPPORTED_WIRE_TYPE);
		}
	}

	/**
	 * Used to aid in pretty printing of Protobuf objects
	 */
	private static $print_depth = 0;
	private static $indent_char = "\t";
	private static $print_limit = 50;

	public static function toString($key, $value) {
		if (is_null($value))
			return;
		$ret = str_repeat(self::$indent_char, self::$print_depth) . "$key=>";
		if (is_array($value)) {
			$ret .= "array(\n";
			self::$print_depth++;
			foreach($value as $i => $v)
				$ret .= self::toString("[$i]", $v);
			self::$print_depth--;
			$ret .= str_repeat(self::$indent_char, self::$print_depth) . ")\n";
		} else {
			if (is_object($value)) {
				self::$print_depth++;
				$ret .= get_class($value) . "(\n";
				$ret .= $value->__toString() . "\n";
				self::$print_depth--;
				$ret .= str_repeat(self::$indent_char, self::$print_depth) . ")\n";
			} elseif (is_string($value)) {
				$safevalue = addcslashes($value, "\0..\37\177..\377");
				if (strlen($safevalue) > self::$print_limit) {
					$safevalue = substr($safevalue, 0, self::$print_limit) . '...';
				}

				$ret .= '"' . $safevalue . '" (' . strlen($value) . " bytes)\n";
				
			} elseif (is_bool($value)) {
				$ret .= ($value ? 'true' : 'false') . "\n";
			} else {
				$ret .= (string)$value . "\n";
			}
		}
		return $ret;
	}
}
?>
