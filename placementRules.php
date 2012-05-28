<?php
////////////////////////////////////////////////////////////////////////////////
  /// file defines all placement rules used in this system
  /// In addition to listing rules, 
  ///   variable names are defined for each rule, and
  ///   rule names are added to an array arrayOfRules
  /// This definition of rules is done by the addToArrayOfRules function

function addToArrayOfRules ($rule) {
  global $arrayOfRules;
  $ruleName = $rule[0];
  //global $$ruleName;
  //$$ruleName = $rule;
  $size = sizeof($arrayOfRules);
  $arrayOfRules[$size] = $rule;
  }

// define $arrayOfRules as a array that will contain all placement rules
global $arrayOfRules;
$arrayOfRules = array ();

////////////////////////////////////////////////////////////////////////////////
////////////////////////////Adjusted Sem of Math Rule///////////////////////////
addToArrayOfRules(array("adj10","AdjSemOfMath add calcsem mathsem",
                        array("some","calcsem != 0","mathsem != 0")));

//////////////////////////////SAT Real Math/////////////////////////////////

addToArrayOfRules(array("sat2math2AdjRule05", "sat2math2adj add sat2math2 5",
                        array("all", "sat2math2 > 0")));
addToArrayOfRules(array("sat2math2AdjRule08", "sat2math2adj add 0 0",
                        array("all", "sat2math2 <= 0")));
addToArrayOfRules(array("satRealRule10", "satmathreal =field satmath",
                        array("all", "satmath >= sat2math1",
                              "sat2math2adj < satmath")));

addToArrayOfRules(array("satRealRule20", "satmathreal =field sat2math1",
                        array("all", "sat2math1 >= satmath",
                              "sat2math2adj < sat2math1")));

addToArrayOfRules(array("satRealRule30", "satmathreal =field sat2math2adj",
                        array("all", "sat2math2adj >= satmath",
                              "sat2math2adj >= sat2math1")));

////////////////////////////////////////////////////////////////////////////////
////////////////////////////Standardized Test Rules/////////////////////////////


////////////////////exceptional//////////////////
addToArrayOfRules(array("stdRule40", "stdscores = exceptional",
                        array("some",
                              array("all", "satmathreal >= 760",
                                    "actmath = 0"),
                              array("all", "actmath >= 34",
                                    "satmathreal = 0"),
                              array("all", "satmathreal >= 700",
                                    "actmath >= 34"),
                              array("all", "satmathreal >= 760",
                                    "actmath >= 31"))));

//////////////////superior///////////////////////


addToArrayOfRules(array("stdRule50", "stdscores = superior",
                        array("some", 
                              array("all", "satmathreal between 700 759",
                                    "actmath = 0"),
                              array("all", "actmath between 31 33",
                                    "satmathreal = 0"),
                              array("all", "satmathreal >= 760",
                                    "actmath between 1 30"),
                              array("all", "satmathreal between 1 699",
                                    "actmath >= 34"),
                              array("all","satmathreal between 700 759",
                                    "actmath between 29 33"),
                              array("all","actmath between 31 33",
                                    "satmathreal between 660 759"))));

///////////////////high////////////////////

addToArrayOfRules(array("stdRule60", "stdscores = high",
                        array("some",
                              array("all", "satmathreal between 660 699",
                                    "actmath = 0"),
                              array("all", "actmath between 29 30",
                                    "satmathreal = 0"),
                              array("all", "satmathreal between 700 759",
                                    "actmath between 1 28"),
                              array("all", "satmathreal between 660 759",
                                    "actmath between 26 28"),
                              array("all","satmathreal between 600 699",
                                    "actmath between 29 30"),
                              array("all","satmathreal between 1 659",
                                    "actmath between 31 33"))));

///////////////////good////////////////////

addToArrayOfRules(array("stdRule70", "stdscores = good",
                        array("some", 
                              array("all", "satmathreal between 600 659",
                                    "actmath = 0"),
                              array("all", "actmath between 26 28",
                                    "satmathreal = 0"),
                              array("all", "satmathreal between 660 699",
                                    "actmath between 1 25"),
                              array("all", "satmathreal between 600 699",
                                    "actmath between 24 25"),
                              array("all","satmathreal between 550 659",
                                    "actmath between 26 28"),
                              array("all","satmathreal between 1 599",
                                    "actmath between 29 30"))));

///////////////////fair////////////////////

addToArrayOfRules(array("stdRule80", "stdscores = fair",
                        array("some", 
                              array("all", "satmathreal between 550 599",
                                    "actmath = 0"),
                              array("all", "actmath between 24 25",
                                    "satmathreal = 0"),
                              array("all", "satmathreal between 600 659",
                                    "actmath between 1 23"),
                              array("all", "satmathreal between 550 659",
                                    "actmath between 20 23"),
                              array("all", "satmathreal between 480 599",
                                    "actmath between 24 25"),
                              array("all","satmathreal between 1 549",
                                    "actmath between 26 28"))));

///////////////////poor////////////////////

addToArrayOfRules(array("stdRule85", "stdscores = poor",
                        array("some", 
                              array("all", "satmathreal between 480 549",
                                    "actmath = 0"),
                              array("all", "actmath between 20 23",
                                    "satmathreal = 0"),
                              array("all", "satmathreal between 480 599",
                                    "actmath between 1 19"),
                              array("all","satmathreal between 1 549",
                                    "actmath between 20 23"),
                              array("all", "satmathreal between 1 479",
                                    "actmath between 24 25"))));

///////////////////low////////////////////
addToArrayOfRules(array("stdRule90", "stdscores = low",
                        array("some", 
                              array("all","satmathreal between 1 479",
                                    "actmath = 0"),
                              array("all","satmathreal = 0",
                                    "actmath between 1 19"),
                              array("all","satmathreal between 1 479",
                                    "actmath between 1 19"))));

//////////////////unknown/////////////////

addToArrayOfRules(array("stdRule95","stdscores = unknown",
                        array("all","actmath = 0",
                              "satmathreal = 0")));



////////////////////////////////////////////////////////////////////////////////
////////////////////////////CS RULES//////////////////////////////////////////


//////////Preliminary Placement in CSC-200
//Rule 210
//                     RuleID     Conclusion
addToArrayOfRules(array("csRule210","TCS-PLACE = 200",
                        array("all","apcsab >= 4")));//Conditions

//Rule 212
addToArrayOfRules(array("csRule212","TCS-PLACE = 200",
                        array("all","ibcs >= 6")));

//////////Preliminary Placement in CSC-153

//Rule 220
addToArrayOfRules(array("csRule220","TCS-PLACE = 153",
                        array("some","apcsa >= 4","apcsab >= 3")));

//Rule 222
addToArrayOfRules(array("csRule222","TCS-PLACE = 153",
                        array("all","ibcs >= 4")));

//Rule 224
addToArrayOfRules(array("csRule224","TCS-PLACE = 153",
                        array("all","cssem >= 3",
                              "apcsa != 1", "apcsa != 2", "apcsa != 3",
                              "apcsab != 1", "apcsab != 2",
                              "ibcs != 1","ibcs != 2","ibcs != 3",
                              "csgrade >= B",
                              "stdscores != low", "stdscores != poor")));

//Rule 226
addToArrayOfRules(array("csRule226","TCS-PLACE = 153",
                        array("all","cssem >= 4",
                              "apcsa != 1","apcsa != 2", "apcsa != 3",
                              "apcsab != 1", "apcsab != 2",
                              "ibcs != 1","ibcs != 2","ibcs != 3",
                              "csgrade >= C",
                              "stdscores != low", "stdscores != poor")));


//////////Preliminary Placement in CSC-152
//Rule 230
addToArrayOfRules(array("csRule230","TCS-PLACE = 152",
                        array("all","stdscores = exceptional")));

//////////Preliminary Placement in CSC-151

//Rule 240
addToArrayOfRules(array("csRule240","TCS-PLACE = 151",
                        array("some","apcsa >= 1",
                              "apcsab >= 1",
                              "ibcs >= 1")));

//Rule 242
addToArrayOfRules(array("csRule242","TCS-PLACE = 151",
                        array("some","MATH-PLACEMENT >= 131")));

//Rule 244
addToArrayOfRules(array("csRule244","TCS-PLACE = 151",
                        array("some","stdscores >= good")));

//Rule 246
addToArrayOfRules(array("csRule246","TCS-PLACE = 151",
                        array("all","cssem >= 1","csgrade >= C",
                              "stdscores != poor",
                              "stdscores != low")));
//Rule 248
addToArrayOfRules(array("csRule248","TCS-PLACE = 151",
                        array("all", "cssem >= 2",
                              array("some","stdscores >= good",
                                    "stdscores = unknown"))));

//preliminary placement in 105

//rule 270
addToArrayOfRules(array("csRule270", "TCS-PLACE = 105",
                        array("some", "stdscores = poor",
                              "stdscores = low")));

//Rule 274 
addToArrayOfRules(array("csRule274", "TCS-PLACE = 105",
                        array("all", "cssem = 1", 
                              "csgrade between F C")));

//preliminary placement in 103
//Rule 280
addToArrayOfRules(array("csRule280", "TCS-PLACE = 103",
                        array("true", "true")));

//Final CS placement

//rule 290
addToArrayOfRules(array("csRule290", "CS-PLACEMENT = 200", 
                        array("all", "TCS-PLACE = 200")));

//csRule292
addToArrayOfRules(array("csRule292", "CS-PLACEMENT = 153",
                        array("all", "TCS-PLACE = 153", 
                              "TCS-PLACE != 200")));

//csRule293
addToArrayOfRules(array("csRule293", "CS-PLACEMENT = 152",
                        array("all", "TCS-PLACE = 152",
                            "TCS-PLACE != 153", 
                              "TCS-PLACE != 200")));

//csRule294
addToArrayOfRules(array("csRule294", "CS-PLACEMENT = 151",
                        array("all", "TCS-PLACE = 151",
                              "TCS-PLACE != 152",
                              "TCS-PLACE != 153",
                              "TCS-PLACE != 200")));

//csRule296
addToArrayOfRules(array("csRule296", "CS-PLACEMENT = 105",
                        array("all", "TCS-PLACE = 105", 
                              "TCS-PLACE != 151",
                              "TCS-PLACE != 152",
                              "TCS-PLACE != 153", 
                              "TCS-PLACE != 200")));


//csRule298
addToArrayOfRules(array("csRule298", "CS-PLACEMENT = 103",
                        array("all","TCS-PLACE = 103",
                              "TCS-PLACE != 105", 
                              "TCS-PLACE != 151",
                              "TCS-PLACE != 152",
                              "TCS-PLACE != 153", 
                              "TCS-PLACE != 200", 
                              "TCS-PLACE != unknown")));

//csRule299
addToArrayOfRules(array("csRule299", "CS-PLACEMENT = unknown",
                        array("all","TCS-PLACE != 103",
                              "TCS-PLACE != 105", 
                              "TCS-PLACE != 151",
                              "TCS-PLACE != 152",
                              "TCS-PLACE != 153", 
                              "TCS-PLACE != 200", 
                              "TCS-PLACE = unknown")));

////////////////////////////////////////////////////////////////////////////////
////////////////////////////STAT RULES//////////////////////////////////////////

//rule 102
addToArrayOfRules(array("statRule102", "TSTAT-PLACE = 115",
                        array("some", "MATH-PLACEMENT <= 132",
                              "MATH-PLACEMENT = unknown")));



///////////////////////208////////////////////////////
//Rule 112
addToArrayOfRules(array("statRule112","TSTAT-PLACE = 208",
                        array("all", "apstat >= 3", 
                              "MATH-PLACEMENT <= 132")));

//Rule 113
addToArrayOfRules(array("statRule113","TSTAT-PLACE = 208",
                        array("all","stdscores between low good",
                              array("some","statsem >= 2",
                                    "apstat >= 1"))));

//rule114
addToArrayOfRules(array("statRule114", "TSTAT-PLACE = 208",
                        array("all","MATH-PLACEMENT <= 131",
                              "stdscores >= high",
                              array("some", "statsem >= 2",
                                    "apstat >= 1"))));
//rule116
addToArrayOfRules(array("statRule116", "TSTAT-PLACE = 208",
                        array("all", "stdscores >= high")));

//rule 118
addToArrayOfRules(array("statRule118","TSTAT-PLACE = 208",
                        array("all","apstat >= 3",
                              "MATH-PLACEMENT = unknown")));

//rule 120
addToArrayOfRules(array("statRule120","TSTAT-PLACE = 208",
                        array("all","stdscores = unknown",
                              array("some","statsem >= 2",
                                    "apstat >= 1"))));
////////////////////////209////////////////////////
//Rule 122
addToArrayOfRules(array("statRule122", "TSTAT-PLACE = 209",
                        array("all", "apstat >= 3",
                              "MATH-PLACEMENT >= 133")));


//Rule124
addToArrayOfRules(array("statRule124", "TSTAT-PLACE = 209",
                        array("all", "MATH-PLACEMENT >= 133")));

//Rule 126

addToArrayOfRules(array("statRule126", "TSTAT-PLACE = 209",
                        array("all","stdscores >= high",
                              "MATH-PLACEMENT >= 133",
                              array("some", "statsem >= 2",
                                    "apstat >= 1"))));

/////////////////////////210///////////////////////////////
//Rule 140
addToArrayOfRules(array("statRule140","TSTAT-PLACE = 210",
                        array("all","apstat >= 4",
                              "MATH-PLACEMENT <= 133",
                              "MATH-PLACEMENT != unknown")));

////////////////////////300///////////////////////////////
  //Rule 150
addToArrayOfRules(array("statRule150","TSTAT-PLACE = 300",
                        array("all","apstat >= 4",
                              "MATH-PLACEMENT >= 215")));


///////////////////////unknown///////////////////////////////
//rule160
addToArrayOfRules(array("statRule160", "TSTAT-PLACE = unknown",
                        array("true", "true")));


//////////////////////Final Stat Rules//////////////////////
//rule170
addToArrayOfRules(array("statRule170","STAT-PLACEMENT = 300",
                        array("all","TSTAT-PLACE = 300")));

//rule172
addToArrayOfRules(array("statRule172","STAT-PLACEMENT = 210",
                        array("all","TSTAT-PLACE = 210",
                              "TSTAT-PLACE != 300")));

//rule174
addToArrayOfRules(array("statRule174","STAT-PLACEMENT = 209",
                        array("all","TSTAT-PLACE = 209",
                              "TSTAT-PLACE != 210",
                              "TSTAT-PLACE != 300")));

//rule176
addToArrayOfRules(array("statRule176","STAT-PLACEMENT = 208",
                        array("all","TSTAT-PLACE = 208",
                              "TSTAT-PLACE != 209",
                              "TSTAT-PLACE != 210",
                              "TSTAT-PLACE != 300")));

//rule180
addToArrayOfRules(array("statRule180","STAT-PLACEMENT = 115",
                     array("all","TSTAT-PLACE = 115",
                           "TSTAT-PLACE != 208",
                           "TSTAT-PLACE != 209",
                           "TSTAT-PLACE != 210",
                           "TSTAT-PLACE != 300")));

addToArrayOfRules(array("statRule190","STAT-PLACEMENT = unknown",
                     array("all","TSTAT-PLACE = unknown",
                           "TSTAT-PLACE != 115",
                           "TSTAT-PLACE != 208",
                           "TSTAT-PLACE != 209",
                           "TSTAT-PLACE != 210",
                           "TSTAT-PLACE != 300")));



////////////////////////////////////////////////////////////////////////////////
////////////////////////////MATH RULES//////////////////////////////////////////

//////////////////// TMATH-PLACE 215 
addToArrayOfRules(array("mathRule102", "TMATH-PLACE = 215",
                        array("some", "apcalcbc >= 4", "ibmat >= 6")));


//////////////////// TMATH-PLACE 133 
addToArrayOfRules(array("mathRule130", "TMATH-PLACE = 133",
                        array("some", "apcalcbc >= 3", "apcalcab >= 4",
                              "ibmat >= 4")));

//calsem >= 3 revised to calsem >= 2, remove alternative stdscores unknown
addToArrayOfRules(array("mathRule135", "TMATH-PLACE = 133",
                        array("all", "calcsem >= 2", 
                              "apcalcab != 1", "apcalcab != 2", 
                              "apcalcbc != 1", "apcalcbc != 2",
                              "ibmat != 1", "ibmat != 2", "ibmat != 3", 
                              "calgrade >= A-",
                              "stdscores >= high")));

//////////////////// TMATH-PLACE 132 
//new rule for new category 132

addToArrayOfRules(array("mathRule140", "TMATH-PLACE = 132",
                        array("all", "calcsem >= 2", 
                              "apcalcab != 1", "apcalcab != 2", 
                              "apcalcbc != 1", "apcalcbc != 2",
                              "ibmat != 1", "ibmat != 2", "ibmat != 3", 
                              "calgrade >= B",
                              array("some", "stdscores >= fair",
                                    "stdscores = unknown"))));

//////////////////// TMATH-PLACE 131 
// ib placement added
addToArrayOfRules(array("mathRule150", "TMATH-PLACE = 131",
                        array("some", "apcalcbc >= 1", 
                              "apcalcab >= 2",
                              "ibmat >= 2")));

//tightened to require stadscores >= good 
addToArrayOfRules(array("mathRule152", "TMATH-PLACE = 131",
                        array("all", "calcsem > 0", 
                              "stdscores >= good")));

//tightened to require act or sat
addToArrayOfRules(array("mathRule153", "TMATH-PLACE = 131",
                        array("all", "precalcsem >= 2",
                              "precalcgrade >= B+",
                              "stdscores >= good")));

//tightened to require act or sat and simplify conditions
addToArrayOfRules(array("mathRule155" , "TMATH-PLACE = 131", 
                        array("all", "AdjSemOfMath >= 6",
                              "stdscores >= high")));


//////////////////// TMATH-PLACE 130
//new rules throughout this section
addToArrayOfRules(array("mathRule162", "TMATH-PLACE = 130",
                        array("all", "calcsem > 0")));

addToArrayOfRules(array("mathRule163", "TMATH-PLACE = 130",
                        array("all", "precalcsem >= 2",
                              "precalcgrade >= B-",
                              "stdscores = unknown")));

addToArrayOfRules(array("mathRule164", "TMATH-PLACE = 130",
                        array("all", "stdscores >= fair")));


addToArrayOfRules(array("mathRule165", "TMATH-PLACE = 130",
                        array("all", "AdjSemOfMath >= 7",
                              "precalcgrade >= B-",
                              "stdscores = unknown")));

//////////////////// TMATH-PLACE 123
addToArrayOfRules(array("mathRule170", "TMATH-PLACE = 123",
                        array("all", "AdjSemOfMath >= 5", 
                              "stdscores != low")));

addToArrayOfRules(array("mathRule172", "TMATH-PLACE = 123",
                        array("all", "stdscores >= poor")));

//////////////////// TMATH-PLACE 100
//placement covers little background, weak standardized scores, or poor grades
addToArrayOfRules(array("mathRule174", "TMATH-PLACE = 100",
                        array("some", "stdscores = low",  
                              "stdscores = poor", 
                              "AdjSemOfMath between 1 4",
                              "precalcgrade between F C-")));

//////////////////// TMATH-PLACE unknown
addToArrayOfRules(array("mathRule178", "TMATH-PLACE = unknown",
                        array("true", "true")));



////////////////final math placements


addToArrayOfRules(array("mathRule182", "MATH-PLACEMENT = 215",
                        array("all", "TMATH-PLACE = 215")));

addToArrayOfRules(array("mathRule185", "MATH-PLACEMENT = 133",
                        array("all", "TMATH-PLACE = 133",
                              "TMATH-PLACE != 215")));

addToArrayOfRules(array("mathRule187", "MATH-PLACEMENT = 132",
                        array("all", "TMATH-PLACE = 132",
                              "TMATH-PLACE != 133",
                              "TMATH-PLACE != 215")));

addToArrayOfRules(array("mathRule190", "MATH-PLACEMENT = 131",
                        array("all", "TMATH-PLACE = 131",
                              "TMATH-PLACE != 215",
                              "TMATH-PLACE != 133",
                              "TMATH-PLACE != 132")));

addToArrayOfRules(array("mathRule192", "MATH-PLACEMENT = 130",
                        array("all", "TMATH-PLACE = 130",
                              "TMATH-PLACE != 215",
                              "TMATH-PLACE != 133",
                              "TMATH-PLACE != 132",
                              "TMATH-PLACE != 131")));

addToArrayOfRules(array("mathRule196", "MATH-PLACEMENT = 123",
                        array("all", "TMATH-PLACE = 123",
                              "TMATH-PLACE != 215",
                              "TMATH-PLACE != 133",
                              "TMATH-PLACE != 132",
                              "TMATH-PLACE != 131",
                              "TMATH-PLACE != 130")));

addToArrayOfRules(array("mathRule198", "MATH-PLACEMENT = 100",
                        array("all", "TMATH-PLACE = 100",
                              "TMATH-PLACE != 215",
                              "TMATH-PLACE != 133",
                              "TMATH-PLACE != 132",
                              "TMATH-PLACE != 131",
                              "TMATH-PLACE != 130",
                              "TMATH-PLACE != 123")));

addToArrayOfRules(array("mathRule200", "MATH-PLACEMENT = unknown",
                        array("all", "TMATH-PLACE = unknown",
                              "TMATH-PLACE != 100",
                              "TMATH-PLACE != 215",
                              "TMATH-PLACE != 133",
                              "TMATH-PLACE != 132",
                              "TMATH-PLACE != 131",
                              "TMATH-PLACE != 130",
                              "TMATH-PLACE != 123")));

?>
