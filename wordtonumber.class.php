<?php
	error_reporting(E_ALL ^ E_NOTICE);
	
	/**
	 * WordToNumber Class
	 * 
	 */ 
	 
	/**
	 * WordToNumbers
	 * 
	 * This class will be able to convert Words into Numbers in a very simple way
	 * @author Muhammad Arfeen
	 * 
	 *
	*/
	
class WordToNumber {
	
	
	function __construct(){
		
	}
	
	/**
	 * This funtion will convert words into numbers
	 * @param $string as string
	 *
	 **/
	 
	function convertToNumbers($string){
		
		// array to search basic numbering
		
		$numbers = array('0'=>"zero",
'1'=>"one",'2'=>"two",'3'=>"three",'4'=>"four",'5'=>"five",'6'=>"six",'7'=>"seven",'8'=>"eight",'9'=>"nine",'10'=>"ten",'11'=>"eleven",'12'=>"twelve",'13'=>"thirteen",'14'=>"fourteen",'15'=>"fifteen",'16'=>"sixteen",'17'=>"seventeen",'18'=>"eighteen",'19'=>"nineteen",'20'=>"twenty",'21'=>"twenty one",'22'=>"twenty two",'23'=>"twenty three",'24'=>"twenty four",'25'=>"twenty five",'26'=>"twenty six",'27'=>"twenty seven",'28'=>"twenty eight",'29'=>"twenty nine",'30'=>"thirty",'31'=>"thirty one",'32'=>"thirty two",'33'=>"thirty three",'34'=>"thirty four",'35'=>"thirty five",'36'=>"thirty six",'37'=>"thirty seven",'38'=>"thirty eight",'39'=>"thirty nine",'40'=>"fourty",'41'=>"fourty one",'42'=>"fourty two",'43'=>"fourty three",'44'=>"fourty four",'45'=>"fourty five",'46'=>"fourty six",'47'=>"fourty seven",'48'=>"fourty eight",'49'=>"fourty nine",'50'=>"fifty",'51'=>"fifty one",'52'=>"fifty two",'53'=>"fifty three",'54'=>"fifty four",'55'=>"fifty five",'56'=>"fifty six",'57'=>"fifty seven",'58'=>"fifty eight",'59'=>"fifty nine",'60'=>"sixty",'61'=>"sixty one",'62'=>"sixty two",'63'=>"sixty three",'64'=>"sixty four",'65'=>"sixty five",'66'=>"sixty six",'67'=>"sixty seven",'68'=>"sixty eight",'69'=>"sixty nine",'70'=>"seventy",'71'=>"seventy one",'72'=>"seventy two",'73'=>"seventy three",'74'=>"seventy four",'75'=>"seventy five",'76'=>"seventy six",'77'=>"seventy seven",'78'=>"seventy eight",'79'=>"seventy nine",'80'=>"eighty",'81'=>"eighty one",'82'=>"eighty two",'83'=>"eighty three",'84'=>"eighty four",'85'=>"eighty five",'86'=>"eighty six",'87'=>"eighty seven",'88'=>"eighty eight",'89'=>"eighty nine",'90'=>"ninety",'91'=>"ninety one",'92'=>"ninety two",'93'=>"ninety three",'94'=>"ninety four",'95'=>"ninety five",'96'=>"ninety six",'97'=>"ninety seven",'98'=>"ninety eight",'99'=>"ninety nine",'100'=>'hundred','1000'=>'thousand','1000000'=>'million');

		$words = explode(" ",$string);
		
		$sw=0;
		$words = array_merge($words);
		
		
		
		
		for($nindex=0;$nindex<count($words);$nindex++){
			
			$numword = $words[$nindex];
			$preservenumword=$numword;
			
			
			if($numword=="one hundred"){
				$numword="hundred";
				$words[$nindex]=$numword;
			}	
			if($numword=="one thousand"){
				$numword="thousand";
				$words[$nindex]=$numword;
			}
			if($numword=="one million"){
				$numword="million";
				$words[$nindex]=$numword;
			}
			
			
			$numwordarray = array_search(strtolower($numword),$numbers);
			
			if($numwordarray===false || $numwordarray==0 || $numwordarray=="" ){				
				unset($words[$nindex]);
			}
			
		}
		
		$finalNumber=0;
		
			for($nindex=0;$nindex<count($words);$nindex++){
				
				
				$number = array_search(strtolower($words[$nindex]),$numbers);
				if($number===false || $number==0 || $number=="")
					continue;
								
				$cur = strtolower($words[$nindex]);
				if($nindex+1 >=count($words))
					$next='';
				else
					$next = strtolower($words[$nindex+1]);
				$coupleword = $cur . " " . $next;
				$couplenumber = array_search($coupleword,$numbers);
				
				if($couplenumber!=false && $couplenumber!=0 && $couplenumber!=""){
					if(strtolower($words[$nindex+2])=="hundred"){
						$finalNumber = $finalNumber + ($couplenumber*100);
						
						$nindex+=2;
						
					}
					elseif(strtolower($words[$nindex+2])=="thousand") {
							
						$finalNumber = ($finalNumber + $couplenumber)*1000;
						
						$nindex+=2;
						
					}
					else{
						$finalNumber = $finalNumber + $couplenumber;
						
						$nindex++;
					}
					
					continue;
				}
				
				
				if(strtolower($words[$nindex])!="thousand" && strtolower($words[$nindex+1])=="hundred"){
					$finalNumber = $finalNumber + ($number * 100);						
					$nindex++;
						
					continue;
				}
				else if(strtolower($words[$nindex+1])=="thousand" && strtolower($words[$nindex])!="hundred"){
					$finalNumber = $finalNumber + ($number * 1000);
					$nindex++;
					
					continue;
				}				
				else if(strtolower($words[$nindex])=="thousand"){
					$finalNumber = $finalNumber * 1000;
					
					continue;
				}				
				else if(strtolower($words[$nindex])=="hundred"){
					$finalNumber = $finalNumber * 100;
					
					continue;
				} 
				else if(strtolower($words[$nindex])=="million"){
					$finalNumber = $finalNumber * 1000000;
					
					continue;
				} 
				else {
					$finalNumber = $finalNumber+$number;
					
				}
				
	}	
	
	return $finalNumber;
	}	
	
}
?>