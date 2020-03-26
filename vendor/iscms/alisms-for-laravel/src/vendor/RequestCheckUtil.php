<?php
namespace iscms\AlismsSdk;

/**
 * API��ξ�̬�����
 * ���Զ�API�Ĳ������͡����ȡ����ֵ�Ƚ���У��
 *
 **/
Class RequestCheckUtil
{
	/**
	 * У���ֶ� fieldName ��ֵ$value�ǿ�
	 *
	 **/
	public static function checkNotNull($value,$fieldName) {
		
		if(self::checkEmpty($value)){
			throw new \Exception("client-check-error:Missing Required Arguments: " .$fieldName , 40);
		}
	}

	/**
	 * �����ֶ�fieldName��ֵvalue �ĳ���
	 *
	 **/
	public static function checkMaxLength($value,$maxLength,$fieldName){		
		if(!self::checkEmpty($value) && mb_strlen($value , "UTF-8") > $maxLength){
			throw new \Exception("client-check-error:Invalid Arguments:the length of " .$fieldName . " can not be larger than " . $maxLength . "." , 41);
		}
	}

	/**
	 * �����ֶ�fieldName��ֵvalue������б���
	 *
	 **/
	public static function checkMaxListSize($value,$maxSize,$fieldName) {	

		if(self::checkEmpty($value))
			return ;

		$list=preg_split("/,/",$value);
		if(count($list) > $maxSize){
				throw new \Exception("client-check-error:Invalid Arguments:the listsize(the string split by \",\") of ". $fieldName . " must be less than " . $maxSize . " ." , 41);
		}
	}

	/**
	 * �����ֶ�fieldName��ֵvalue �����ֵ
	 *
	 **/
	public static function checkMaxValue($value,$maxValue,$fieldName){	

		if(self::checkEmpty($value))
			return ;

		self::checkNumeric($value,$fieldName);

		if($value > $maxValue){
				throw new \Exception("client-check-error:Invalid Arguments:the value of " . $fieldName . " can not be larger than " . $maxValue ." ." , 41);
		}
	}

	/**
	 * �����ֶ�fieldName��ֵvalue ����Сֵ
	 *
	 **/
	public static function checkMinValue($value,$minValue,$fieldName) {
		
		if(self::checkEmpty($value))
			return ;

		self::checkNumeric($value,$fieldName);
		
		if($value < $minValue){
				throw new \Exception("client-check-error:Invalid Arguments:the value of " . $fieldName . " can not be less than " . $minValue . " ." , 41);
		}
	}

	/**
	 * �����ֶ�fieldName��ֵvalue�Ƿ���number
	 *
	 **/
	protected static function checkNumeric($value,$fieldName) {
		if(!is_numeric($value))
			throw new \Exception("client-check-error:Invalid Arguments:the value of " . $fieldName . " is not number : " . $value . " ." , 41);
	}

	/**
	 * У��$value�Ƿ�ǿ�
	 *  if not set ,return true;
	 *	if is null , return true;
	 *	
	 *
	 **/
	public static function checkEmpty($value) {
		if(!isset($value))
			return true ;
		if($value === null )
			return true;
		if(trim($value) === "")
			return true;
		
		return false;
	}

}
?>
