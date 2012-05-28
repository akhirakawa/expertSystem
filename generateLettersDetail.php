<?php
  //this file contains functions used in letter generation
  //as well as several arrays that are used to match
  //placement data to the correct Macro.
///////////////////////////////////////////////
  
//arrays matching placement with correct macro
$mathMacroArray =array(
                       '100' => "\mathLab",
                       '123' => "\calcIntro",
                       '130' => "\calcIntroTwo",
                       '131' => "\calcReg",
                       '132' => "\calcRegTwo",
                       '133' => "\calcTwo",
                       '215' => "\linear",
                       'unknown' => "\mathUnk");
$csMacroArray = array(
                      '103' => "\csGeneral",
                      '105' => "\csRemedial",
                      '151' => "\csIntro",
                      '152' => "\csPushIntro",
		      '153' => "\csTwo",
                      '200' => "\csAdv",
                      'unknown' => "\csUnk");

$statMacroArray = array(
                        '115' => "\statOne",
                        '207' => "\statOnePlus",
                        '208' => "\statTwoMinus",
                        '209' => "\statTwo",
                        '210' => "\statTwoPlus",
                        '300' => "\statAdv",
                        'unknown' => "\statUnk");
//////////////////////////////////////////////////////////////////
//this determines if the student has more then one unknown placement
//and if so it changes the texts accordingly
function multipleUnknown(&$csPlace, &$mathPlace, &$statPlace)
{
  if(($csPlace == '\csUnk') && ($statPlace == '\statUnk'))
    {
      if($mathPlace ==  '\mathUnk')
        {
          $mathPlace = "\mathCSStatUnk";
          $csPlace = "";
          $statPlace = "";
        }
      else
        {
          $csPlace = "\CSStatUnk";
	  $statPlace = "";
        }
    }
  if(($csPlace == '\csUnk') && ($mathPlace == '\mathUnk'))
    {
      $csPlace = "\mathCSUnk";
      $mathPlace ="";
    }
  if(($statPlace == '\statUnk') && ($mathPlace == '\mathUnk'))
    {
      $mathPlace = "\mathStatUnk";
      $statPlace = "";
    }

  return(null);
}
///////////////////////////////////////////////////////////////////////

//takes and input, if the value is null, return 0, otherwise retrurn input
function safeInput($num)
{
  if(empty($num) )
    {
      return(0);
    }
  else
    {
      return($num);
    }
}

///////////////////////////////////////////////////////////////////////
// This function takes data for a student and returns 
// a sting containing the proper transcript data
function transcriptStringMaker($studentData)
{
  //gather student transcript data
  $mathsem = $studentData['mathsem'];
  if (empty($mathsem))
    $mathsem = 0;
  $precalcsem = $studentData['precalcsem'];
  $precalcgrade = $studentData['precalcgrade'];
  $calcsem = $studentData['calcsem'];
  $calgrade = $studentData['calgrade']; 
  $cssem = $studentData['cssem'];
  $csgrade = $studentData['csgrade'];
  $statsem = $studentData['statsem'];
  $statgrade = $studentData['statgrade'];
  $apcalcab = $studentData['apcalcab'];
  $apcalcbc = $studentData['apcalcbc'];
  $apcsa = $studentData['apcsa'];
  $apcsab = $studentData['apcsab'];
  $apstat = $studentData['apstat'];
  $ibmat = $studentData['ibmat'];
  $ibcs = $studentData['ibcs'];
  $alevelf = $studentData['alevelf'];
  $alvelp = $studentData['alevelp'];
  $actcomp = $studentData['actcomp'];
  $actmath = $studentData['actmath'];
  $satmath = $studentData['satmath'];
  
  
  //Begining of the transcript information
  $transString = "\mathSemMacro{".$mathsem."}";
  if($precalcsem > 0)
    {
      $transString .= "\n\\precalcMacro{".$precalcsem."}".
        "{".$precalcgrade."}  ";
    }
  else
    {
      $transString .= "\\begin{packed_enum}\item[]\\vspace{-.175in}";
    }
  //add clac line in neccesary
  if($calcsem > 0)
    {
      $transString .= "\n\\calcMacro{".safeInput($calcsem)."}"."{".safeInput($calgrade)."} ";
   }
  else
    {
      $transString .= "\\end{packed_enum}";
    }
  //add cs, and stat lines
  $transString .= "\n\csMacro{".safeInput($cssem)."}{".safeInput($csgrade)."}  ";
  $transString .= "\n\statMacro{".safeInput($statsem)."}{".safeInput($statgrade)."}  ";
  $transString .= "\\vspace{-.1in}";
 
 //add AP line if necesary, and include appropriate Tests
 if($apcalcab || $apcalcbc || $apstat || $apcsa || $apcsab)
   {
     $transString .= "\n\apMacro{".safeInput($apcalcab)."}{".
       safeInput($apcalcbc)."}{".safeInput($apstat)."}{".safeInput($apcsa)."}{".safeInput($apcsab)."}  ";
   }
 
 //add statndardized test scores if necesary, and include proper tests
 if($actcomp || $actmath || $satmath)
   {
     $transString .= "\n\\stdTestMacro{".safeInput($actcomp)."}{".safeInput($actmath)."}{".
       safeInput($satmath)."}";
   }
 
 //add IB line in necesary, and include proper tests
 if($ibmath || $ibcs)
   {
     $transString .= "\\\ibMacro{".safeInput($ibmath)."}{".safeInput($ibcs)."}";
   }
 
 //add alevel line if necesary, and include proper tests
 if($alevelf || $alevelp)
   {
     $transString .= "\\\alevelMacro{".safeInput($alevelf)."}{".safeInput($alevelp)."}";
   }
 return($transString);
}


?>
