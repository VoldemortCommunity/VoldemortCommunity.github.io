<?php

/*
* Created By @Voldemort1912
* First Global Release v1.0
* Last Update: 20/04/2019
* Time: 2200
* Telegram @hewhomustnotbenamed
*/

//RO NOT MODIFY

class DateChecker
{
	function isvalid($link, $expiryraw){
	
		list($expdays, $expmonths, $expyears) = explode("-",$expiryraw);
		$expiry = $expdays + (int) ($expmonths * 30.4167 + $expyears * 365.5);
		
		if($expiry == 0){
			return true;
		} else {
			$page = @file_get_contents($link);
			if(strpos($page, '<img src="/i/t.gif" class="img_line t_da" alt="">')){
				$startpos = strpos($page, '<img src="/i/t.gif" class="img_line t_da" alt="">')+50;
				$endpos = strpos($page, "</span>", $startpos);
				$length = $endpos-$startpos;
				$rawdate = substr($page, $startpos, $length);
				$date = substr($rawdate, strpos($rawdate, ">")+1);
				
				$pastedate = new DateTime($date);
				$today   = new DateTime('today');
				$age = $pastedate->diff($today);
				
				
				$days = $age->days;
				//print($days."\n");
			
				list($expdays, $expmonths, $expyears) = explode("-",$expiryraw);
			
				$expiry = $expdays + (int) ($expmonths * 30.4167 + $expyears * 365.5);
				//print($expiry."\n");
				
				if($days<=$expiry)
					return true;
				else
					return false;
			} else {
				return false;
			}
		}
	}
}

/*
Modified For Usage by @hewhomustn0tbenamed (Telegram)
Credits : Samay Bhavsar
Version : 1.2 - KillHarry
*/

class GoogleScraper
{
	var $keyword	=	"testing";
	var $urlList	=	array();
	var $time1		=	4000000;
	var $time2		=	8000000;
	var $proxy		=	"";
	var $cookie		=	"";
	var $header		=	"";
	var $ei			=	"";


	function __construct() {
		$this->cookie = tempnam ("/tmp", "cookie");
		$this->headers[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$this->headers[] = "Connection: keep-alive";
		$this->headers[] = "Keep-Alive: 115";
		$this->headers[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$this->headers[] = "Accept-Language: en-us,en;q=0.5";
		$this->headers[] = "Pragma: ";
	}

	function getpagedata($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 5.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1');
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($ch, CURLOPT_COOKIEFILE,  $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR,  $this->cookie);
		curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		$data=curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	function pause() {
		usleep(rand($this->time1,$this->time2));
	}

	function initGoogle() {
		$data=$this->getpagedata('http://www.google.com');		//	Open google.com ( Might redirect to country specific site e.g. www.google.co.in)
		$this->pause();
		$this->getpagedata('http://www.google.com/ncr');	//	Moves back to google.com
	}


	// This function opens the preference page and saves the count for "Results per page" to 100
	function setPreference() {
		$data=$this->getpagedata('http://www.google.com/preferences?hl=en');
		preg_match('/<input type="hidden" name="sig" value="(.*?)">/', $data, $matches);
		$this->pause();
		$this->getpagedata('http://www.google.com/setprefs?sig='.urlencode($matches[1]).'&hl=en&lr=lang_en&safeui=images&suggon=2&newwindow=0&num=100&q=&prev=http%3A%2F%2Fwww.google.com%2F&submit2=Save+Preferences+');
	}

	function fetchUrlList()
	{
		for($i=0;$i<1001;$i=$i+100) {
			$data=$this->getpagedata('http://www.google.com/search?q='.$this->keyword.'&num=100&hl=en&biw=1280&bih=612&prmd=ivns&ei='.$this->ei.'&start='.$i.'&sa=N');
			preg_match('/;ei=(.*?)&amp;/', $data, $matches);
			if(empty($matches))
			{
				preg_match('/;sei=(.*?)"/', $data, $matches);
				$this->ei=urlencode($matches[1]);

				if(empty($matches))
				{
					file_put_contents("data.html",$data);
					exit();
				}
			} else {
				$this->ei=urlencode($matches[1]);
			}

			if ($data) {
				if(preg_match("/sorry.google.com/", $data)) {
					echo "You are blocked";
					exit;
				} else {
					preg_match_all('@<h3\s*class="r">\s*<a[^<>]*href="[^<>]*?q=([^<>]*)&amp;sa[^<>]*>(.*)</a>\s*</h3>@siU', $data, $matches);
					for ($j = 0; $j < count($matches[1]); $j++) {
						array_push($this->urlList, $matches[1][$j]);
					}
				}
			}
			else
			{
				echo "Problem fetching the data";
				exit;
			}
			$this->pause();
		}
	}

	function getUrlList($keyword,$proxy='') {
		$this->keyword=$keyword;
		$this->proxy=$proxy;
		$this->initGoogle();
		$this->pause();
		$this->setPreference();
		$this->pause();
		$this->fetchUrlList();
		return $this->urlList;
	}
}

ini_set ( 'max_execution_time', 0);

// DO NOT MODIFY!! I WILL NOT BE RESPONSIBLE FOR ANY KIND OF MODIFICATIONS WHICH HINDER THE FUNCTIONABILITY OF THE PROGRAM!!!
$loc_ver = trim(@file_get_contents('.version'));
//FIRST RUN (Install Dependencies)
if (is_file(".first")){
	$firstrun = trim(@file_get_contents('.first'));
}else{
	$firstrun = true;
}
if ($firstrun == 'true'){
	echo "\033[01;32;1m[i] First Run Detected... Installing Dependencies.....";
	echo "\n[i] This Might Take a While! PLEASE DO NOT EXIT THE PROGRAM!...\n";
	sleep(1);
	echo "[#####";
	sleep(2);
	echo "##";
	sleep(1);
	echo "###";
		system('apt install curl python python2 figlet -y > /dev/null 2> /dev/null');
	for ($i=0; $i<15; $i++){
		echo "#"; 
		sleep(1);
	};
	echo "]";
	sleep(3);
	echo "\n[i] The Dependencies Have Been Installed Successfully!!";
	echo "\n[i] The Program has Been Installed Successfully!";
	echo "\n[i] Initiating Setup!!";
	sleep(3);
	echo "\n[i] Setup Complete.";
	echo "\n[i] Please Restart the Program to See the Changes";
	echo "\n[i] Press Enter to Exit...";
	fgetc(STDIN);
	file_put_contents('.first','false');
	exit;
	}

$b = "\033[1m";
$R = "\033[91m";
$G = "\033[92m";
$Y = "\033[93m";
$B = "\033[94m";
$C = "\033[96m";
$X = "\033[0m";
$n = "\n";
$t = "\t";

print("$C$b");
print("
 _____           _       _____
|     |___ _____| |_ ___|   __|___ ___
|   --  . |     | . | . |  |  | -_|   |
|_____|___|_|_|_|___|___|_____|___|_|_| v$loc_ver by @hewhomustn0tbenamed

Github: https://github.com/VoldemortCommunity/ComboGen

$X");

sleep(2);
echo "\033[01;31m[?] v".$loc_ver." Developed by \033[01;32;1m@hewhomustn0tbenamed\033[01;31m (Telegram).\n";
sleep(3);
echo "\033[01;31m[?] You're Responsible For your Actions. Use Wisely.\n";
sleep(1);
echo "[?] Join \033[01;32;1m@VoldemortCommunity (Telegram)\033[01;31m for More!.\n";
sleep(0.8);
echo "[?] Huge Thanks to Samay Bhavsar for His Google Scraping Mechanism.\n";
sleep(0.8);
echo "[?] Thank You to all the Testers!! and StackOverflow.\n\033[0m";
sleep(2);

//Check Updates
$glo_ver = trim(@file_get_contents('https://raw.githubusercontent.com/VoldemortCommunity/ComboGen/master/.version'));
$loc_ver = trim(@file_get_contents('.version'));
if($glo_ver > $loc_ver){
	echo "\033[01;32;1m[i] Update Available!!\n";
	$changes = @file_get_contents("https://raw.githubusercontent.com/VoldemortCommunity/ComboGen/master/.changelog");
	echo "[i] Changelog : \n".$changes;
	sleep(5);
	echo "\n\033[51;33;1m[i] Do you Want to Update to v".$glo_ver."?? Type \033[01;32;1m'yes'\033[51;33;1m to Continue :\033[0m ";
	$updatehandle = fopen("php://stdin", "rb");
	$ln = fgets($updatehandle);
	if (trim($ln) == 'yes'){
		echo "\033[05;32;1m[i] Updating Now...\n[i] Press Enter to Start...\033[0m";
		fgetc(STDIN);
		system('git reset --hard');
		system('git pull origin master');
		echo "\033[01;32;1m[i] Update Complete!! Please Restart to see Changes!!";
		echo "\n[i] Press Enter to Continue...";
		fgetc(STDIN);
		exit;
	}
} else {
	echo $G."[i] You're Already on the Latest Version!! Cheers!$X";
}

//Warning
echo "\n\033[51;33;1m[i] Are You Sure You Want To Do This?  Type \033[01;32;1m'yes'\033[51;33;1m to Continue :\033[0m ";
$warnhandle = fopen("php://stdin", "rb");
$ln = fgets($warnhandle);
if (trim($ln) !== 'yes'){
	echo "\033[05;31m[i] ABORT.\n[i] The Program will Now Exit!!\n[i] Press Enter to Continue...\033[0m";
	fgetc(STDIN);
	exit;
}


$tempfile = ".temp";
$outfile = 'combo.txt';

print($G."[i] Enter File Name to Store Combos (Default : 'combo.txt') : ".$X);
$filename = trim(fgets(fopen("php://stdin","rb")));

if(!empty($filename)){
	if(strpos($filename,'.txt')){
		$outfile = $filename;
	} else {
		$outfile = $filename.'.txt';
	}
} else {
	$outfile = 'combo.txt';
}

system('rm -rf $tempfile');
system('rm -rf $outfile');
$google = new GoogleScraper();
$expchk = new DateChecker();

keywords:{
print($G."[i] Load Keywords From File? ".$n."[i] Enter Filename or Leave Empty to Enter Keywords Manually : ".$X);
$keywordinput = trim(fgets(fopen("php://stdin","rb")));
if ($keywordinput != NULL){
	if(is_file($keywordinput))
		$keywords = file($keywordinput);
	else {
		print($R."[i] File Does Not Exist. Make Sure you Entered the Correct File Name.".$X.$n);
		goto keywords;
	}
} else {
	print($G."[i] Enter Keywords Seperated by SemiColons(;) : ".$X);
	$keywords = explode(";",(fgets(fopen("php://stdin", "rb"))));
}
}
freshness:{
print($G."[i] Enter Paste Freshness [Format : dd-mm-yy]".$n."[i] Default Value = 0-0-0 (No Conditions)  : ");
$expuser = trim(fgets(fopen("php://stdin","rb")));
$datepattern = '~^(\d{1,2})-(\d{1,2})-(\d{1,2})$~';

if($expuser == NULL)
	$expuser = "0-0-0";

if(!preg_match($datepattern, $expuser)){
	print($n.$R."[i] Invalid Format Please Enter in the Form dd-mm-yy.".$X.$n);
	goto freshness;
}
}
$links = array();
print("$n$n$G"."[i] Start Scanning$X$n$n");
foreach((array)$keywords as $keyword){
	$keyword = trim($keyword);
	if ($keyword != NULL){
		print($G."[i] Scanning Keyword : $keyword".$X.$n);
		$dork = "site:pastebin.com intext:".$keyword;
		$arr = $google->getUrlList(urlencode("$dork"),'');
		if(empty($arr)){
			print($R."[i] Your IP Address has been Blocked... Consider Using a VPN or Try Again Later...".$X.$n);
			//exit();
		} else {
			print($G."[i] ".count($arr)." Links Found!!$n$n");
			foreach($arr as $sinlink){
				array_push($links, $sinlink);
			}
		}
	} 
}

if(empty($links)){
	print($R."[i] No Links were Collected. Check For Errors Above & Verify Your Internet Connection$n"."[i] If the Problem Persists Please Contact @hewhomustn0tbenamed (Telegram)!$X");
	exit();
} else {
	print($G."[i] ".count($links)." URLs Collected.$n[i] Begin Leeching.$n".$X);
	sleep(5);
}


$count = 0;
foreach($links as $link){
	print($Y."[i] Link : $link$n".$X);
	//sleep(1);
	print($Y."[i] Leeching...$n$n".$X);
	//sleep(2);
	if($expchk->isvalid($link, $expuser)){
		$rawpagelink = substr($link, 0, strpos($link, '.com/')).'.com/raw/'.substr($link, strpos($link, '.com/')+5);
		$pastedata = @file_get_contents($rawpagelink);
		file_put_contents($tempfile, $pastedata.$n.$n);
		$dataarray = file($tempfile);
		
		foreach($dataarray as $rawline){
			if (strpos($rawline, '|')) {
				$purified = explode("|", $rawline);
				$rawline = trim($purified[0]);
			}
			if (strpos($rawline, ':') && strpos($rawline, '@')) {
	        	list($mail,$pass) = explode(":", $rawline);
	            $mail = trim($mail);
	            $pass = trim($pass);
	            $mailpattern = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,9}$/";
	            $passpattern = '/^[0-9A-Za-z!@#$?%._-]{2,32}$/';
	                        
	            if(preg_match($mailpattern, $mail) && preg_match($passpattern, $pass)){
	            	print($t.$G."[i] Combo Found : $mail:$pass".$n.$X);
	            	file_put_contents($outfile, "$mail:$pass$n", FILE_APPEND);
	                $count++;
	            }
			}
		}
	} else {
		print($t.$G."[i] Paste does not Match Expiry Conditions!".$n.$X);
		
	}
}

print($G."[i] $count Combos have been Saved to $outfile...$X$n");
system('rm -rf *.html '.$tempfile);