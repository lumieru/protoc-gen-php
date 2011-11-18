<?php
// Please include the below file before response.proto.php
require_once 'protocolbuffers.inc.php';
// message SDServer.Response
class SDServer_Response {
  private $_unknown;
  
  function __construct($in = NULL, &$limit = PHP_INT_MAX) {
    if($in !== NULL) {
      if (is_string($in)) {
        $fp = fopen('php://memory', 'r+b');
        fwrite($fp, $in);
        rewind($fp);
      } else if (is_resource($in)) {
        $fp = $in;
      } else {
        throw new Exception('Invalid in parameter', EXP_CODE_INVALID_PARAMETER);
      }
      $this->read($fp, $limit);
    }
  }
  
  function read($fp, &$limit = PHP_INT_MAX) {
    while(!feof($fp) && $limit > 0) {
      $tag = Protobuf::read_varint($fp, $limit);
      if ($tag === false) break;
      $wire  = $tag & 0x07;
      $field = $tag >> 3;
      //var_dump("SDServer_Response: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
      switch($field) {
        case 1:
          ASSERT('$wire == 0');
          $this->code_ = Protobuf::read_varint($fp, $limit);
          
          break;
        case 2:
          ASSERT('$wire == 0');
          $this->gid_ = Protobuf::read_varint($fp, $limit);
          
          break;
        case 3:
          ASSERT('$wire == 2');
          $this->uid_ = Protobuf::read_string($fp, $limit);
          
          break;
        case 4:
          ASSERT('$wire == 0');
          $this->vc_ = Protobuf::read_varint($fp, $limit);
          
          break;
        case 5:
          ASSERT('$wire == 0');
          $this->pvg_ = Protobuf::read_varint($fp, $limit);
          
          break;
        case 6:
          ASSERT('$wire == 0');
          $this->eg_ = Protobuf::read_varint($fp, $limit);
          
          break;
        case 7:
          ASSERT('$wire == 0');
          $this->et_ = Protobuf::read_varint($fp, $limit);
          
          break;
        case 8:
          ASSERT('$wire == 2');
          $this->bb_ = Protobuf::read_string($fp, $limit);
          
          break;
        case 9:
          ASSERT('$wire == 0');
          $this->bv_ = Protobuf::read_varint($fp, $limit);
          
          break;
        case 10:
          ASSERT('$wire == 0');
          $this->errorCode_ = Protobuf::read_varint($fp, $limit);
          
          break;
        case 11:
          ASSERT('$wire == 2');
          $this->errorMsg_ = Protobuf::read_string($fp, $limit);
          
          break;
        default:
          $this->_unknown[$field . '-' . Protobuf::get_wiretype($wire)][] = Protobuf::read_field($fp, $wire, $limit);
      }
    }
    if (!$this->validateRequired())
      throw new Exception('Required fields are missing', EXP_CODE_MISS_REQUIRED_FIELDS);
  }
  
  function write($fp) {
    if (!$this->validateRequired())
      throw new Exception('Required fields are missing', EXP_CODE_MISS_REQUIRED_FIELDS);
    if (!is_null($this->code_)) {
      fwrite($fp, "\x08");
      Protobuf::write_varint($fp, $this->code_);
    }
    if (!is_null($this->gid_)) {
      fwrite($fp, "\x10");
      Protobuf::write_varint($fp, $this->gid_);
    }
    if (!is_null($this->uid_)) {
      fwrite($fp, "\x1a");
      Protobuf::write_string($fp, $this->uid_);
    }
    if (!is_null($this->vc_)) {
      fwrite($fp, " ");
      Protobuf::write_varint($fp, $this->vc_);
    }
    if (!is_null($this->pvg_)) {
      fwrite($fp, "(");
      Protobuf::write_varint($fp, $this->pvg_);
    }
    if (!is_null($this->eg_)) {
      fwrite($fp, "0");
      Protobuf::write_varint($fp, $this->eg_);
    }
    if (!is_null($this->et_)) {
      fwrite($fp, "8");
      Protobuf::write_varint($fp, $this->et_);
    }
    if (!is_null($this->bb_)) {
      fwrite($fp, "B");
      Protobuf::write_string($fp, $this->bb_);
    }
    if (!is_null($this->bv_)) {
      fwrite($fp, "H");
      Protobuf::write_varint($fp, $this->bv_);
    }
    if (!is_null($this->errorCode_)) {
      fwrite($fp, "P");
      Protobuf::write_varint($fp, $this->errorCode_);
    }
    if (!is_null($this->errorMsg_)) {
      fwrite($fp, "Z");
      Protobuf::write_string($fp, $this->errorMsg_);
    }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->code_)) {
      $size += 1 + Protobuf::size_varint($this->code_);
    }
    if (!is_null($this->gid_)) {
      $size += 1 + Protobuf::size_varint($this->gid_);
    }
    if (!is_null($this->uid_)) {
      $l = strlen($this->uid_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->vc_)) {
      $size += 1 + Protobuf::size_varint($this->vc_);
    }
    if (!is_null($this->pvg_)) {
      $size += 1 + Protobuf::size_varint($this->pvg_);
    }
    if (!is_null($this->eg_)) {
      $size += 1 + Protobuf::size_varint($this->eg_);
    }
    if (!is_null($this->et_)) {
      $size += 1 + Protobuf::size_varint($this->et_);
    }
    if (!is_null($this->bb_)) {
      $l = $this->bb_->$length;
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->bv_)) {
      $size += 1 + Protobuf::size_varint($this->bv_);
    }
    if (!is_null($this->errorCode_)) {
      $size += 1 + Protobuf::size_varint($this->errorCode_);
    }
    if (!is_null($this->errorMsg_)) {
      $l = strlen($this->errorMsg_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    return $size;
  }
  
  public function validateRequired() {
    if ($this->code_ === null) return false;
    return true;
  }
  
  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('code_', $this->code_)
         . Protobuf::toString('gid_', $this->gid_)
         . Protobuf::toString('uid_', $this->uid_)
         . Protobuf::toString('vc_', $this->vc_)
         . Protobuf::toString('pvg_', $this->pvg_)
         . Protobuf::toString('eg_', $this->eg_)
         . Protobuf::toString('et_', $this->et_)
         . Protobuf::toString('bb_', $this->bb_)
         . Protobuf::toString('bv_', $this->bv_)
         . Protobuf::toString('errorCode_', $this->errorCode_)
         . Protobuf::toString('errorMsg_', $this->errorMsg_);
  }
  
  // required uint32 code = 1;

  private $code_ = null;
  public function clearCode() { $this->code_ = null; }
  public function hasCode() { return $this->code_ !== null; }
  public function getCode() { if($this->code_ === null) return 0; else return $this->code_; }
  public function setCode($value) { $this->code_ = $value; }
  
  // optional uint32 gid = 2;

  private $gid_ = null;
  public function clearGid() { $this->gid_ = null; }
  public function hasGid() { return $this->gid_ !== null; }
  public function getGid() { if($this->gid_ === null) return 0; else return $this->gid_; }
  public function setGid($value) { $this->gid_ = $value; }
  
  // optional string uid = 3;

  private $uid_ = null;
  public function clearUid() { $this->uid_ = null; }
  public function hasUid() { return $this->uid_ !== null; }
  public function getUid() { if($this->uid_ === null) return ""; else return $this->uid_; }
  public function setUid($value) { $this->uid_ = $value; }
  
  // optional uint32 vc = 4;

  private $vc_ = null;
  public function clearVc() { $this->vc_ = null; }
  public function hasVc() { return $this->vc_ !== null; }
  public function getVc() { if($this->vc_ === null) return 0; else return $this->vc_; }
  public function setVc($value) { $this->vc_ = $value; }
  
  // optional uint32 pvg = 5;

  private $pvg_ = null;
  public function clearPvg() { $this->pvg_ = null; }
  public function hasPvg() { return $this->pvg_ !== null; }
  public function getPvg() { if($this->pvg_ === null) return 0; else return $this->pvg_; }
  public function setPvg($value) { $this->pvg_ = $value; }
  
  // optional uint32 eg = 6;

  private $eg_ = null;
  public function clearEg() { $this->eg_ = null; }
  public function hasEg() { return $this->eg_ !== null; }
  public function getEg() { if($this->eg_ === null) return 0; else return $this->eg_; }
  public function setEg($value) { $this->eg_ = $value; }
  
  // optional uint32 et = 7;

  private $et_ = null;
  public function clearEt() { $this->et_ = null; }
  public function hasEt() { return $this->et_ !== null; }
  public function getEt() { if($this->et_ === null) return 0; else return $this->et_; }
  public function setEt($value) { $this->et_ = $value; }
  
  // optional bytes bb = 8;

  private $bb_ = null;
  public function clearBb() { $this->bb_ = null; }
  public function hasBb() { return $this->bb_ !== null; }
  public function getBb() { if($this->bb_ === null) return ""; else return $this->bb_; }
  public function setBb($value) { $this->bb_ = $value; }
  
  // optional uint32 bv = 9;

  private $bv_ = null;
  public function clearBv() { $this->bv_ = null; }
  public function hasBv() { return $this->bv_ !== null; }
  public function getBv() { if($this->bv_ === null) return 0; else return $this->bv_; }
  public function setBv($value) { $this->bv_ = $value; }
  
  // optional uint32 error_code = 10;

  private $errorCode_ = null;
  public function clearErrorCode() { $this->errorCode_ = null; }
  public function hasErrorCode() { return $this->errorCode_ !== null; }
  public function getErrorCode() { if($this->errorCode_ === null) return 0; else return $this->errorCode_; }
  public function setErrorCode($value) { $this->errorCode_ = $value; }
  
  // optional string error_msg = 11;

  private $errorMsg_ = null;
  public function clearErrorMsg() { $this->errorMsg_ = null; }
  public function hasErrorMsg() { return $this->errorMsg_ !== null; }
  public function getErrorMsg() { if($this->errorMsg_ === null) return ""; else return $this->errorMsg_; }
  public function setErrorMsg($value) { $this->errorMsg_ = $value; }
  
  // @@protoc_insertion_point(class_scope:SDServer.Response)
}

