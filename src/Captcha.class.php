<?php
	namespace Vendor;

	class Captcha{
		//定义相关属性
		private $width; //宽
		private $height; //高
		private $pixelnum;//干扰点密度
		private $linenum;//干扰线数量
		private $stringnum;//验证码字符的个数
		private $string; //要写入的随机字符串

		//字体
		private $font;

		public function __construct($arr=array()){
		
			$this->width = isset($arr['width']) ? $arr['width'] : '';
			$this->height = isset($arr['height']) ? $arr['height'] : '';
			$this->pixelnum = isset($arr['pixelnum']) ? $arr['pixelnum'] : '';
			$this->linenum = isset($arr['linenum']) ? $arr['linenum'] : '';
			$this->stringnum = isset($arr['stringnum']) ? $arr['stringnum'] : '';
			$this->string = isset($arr['string']) ? $arr['string'] : '';
			$this->font   = isset($arr['font']) ? $arr['font']   : "";
			
		}

		//生成验证码图片
	

		public function generate(){
			//1 创建画布
			//2 设置背景色
			//3 设置干扰点
			//4 设置干扰线
			//5 填充验证码字符串
			//6 输出验证码字符串
			//7 消除资源
			//1,创建画布
			$img = imagecreatetruecolor($this->width,$this->height);

			//设置背景色

			$bgcolor = imagecolorallocate($img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
			//填充背景

			imagefill($img,0,0,$bgcolor);

			//得到随机产生验证码
			$this->string = $this->getCaptchaString();
			
			//验证码写到图片上
			//计算字符间隔
			$span = ceil($this->width/($this->stringnum+1));
			
			//循环写入单个字符
			for($i = 1 ; $i <= $this->stringnum; $i++){
				$strcolor = imagecolorallocate($img,mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
				imagettftext($img,18,20,$span*$i,mt_rand(18,25),$strcolor,$this->font,$this->string[$i-1]);
				
			}

			//设置干扰线
			for($i = 1 ; $i <= $this->linenum ; $i++){
				$linecolor = imagecolorallocate($img,mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
				imageline($img,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$linecolor);
			}

			//设置干扰点
			for($i = 1 ; $i <= $this->width*$this->height*$this->pixelnum; $i++){
				$pixelcolor = imagecolorallocate($img,mt_rand(100,150),mt_rand(0,120),mt_rand(0,255));
				imagesetpixel($img,mt_rand(0,$this->width-1),mt_rand(0,$this->height-1),$pixelcolor);
			}

			//输出图片
			header('Content-type:image/png');
			ob_clean();
			imagepng($img);

			//销毁图片
			imagedestroy($img);
		}

		//返回随机获得的字符串
		private function getCaptchaString(){
			//得到字符串,随机的$this->length
			$string = '';
			//从大写字母,小写字母和数字1-9中取
			for($i = 0 ; $i < $this->stringnum ; $i++){
				//a-z 97-122//A-Z 65-90 //1-9 49-57
				switch(mt_rand(1,3)){ //随机

					case 1://取小写字母
					$string.=chr(mt_rand(97,122)); 
					break;

					case 2://取大写字母
					$string.=chr(mt_rand(65,90)); 
					break;

					case 3://取数字
					$string.=chr(mt_rand(49,57)); 
					break;
				}
			}
			
			$_SESSION['captcha'] = $string;
			//返回验证码
			return $string;
			
		}

		//验证验证码方法
		public static function checkCaptcha($captcha){
			$temp = isset($_SESSION['captcha']) ? strtolower($_SESSION['captcha']) : ' ';
			return strtolower($captcha) === $temp;
		}
	}
?>