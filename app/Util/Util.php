<?php

/**
 * Class for generic utility functions.
 *
 * @author sfalc
 */
class Util {
    static function addOrdinalNumberSuffix($num) {
        if (!in_array(($num % 100), array(11, 12, 13))) {
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1: return number_format($num, 0) . 'st';
                case 2: return number_format($num, 0) . 'nd';
                case 3: return number_format($num, 0) . 'rd';
            }
        }
        return number_format($num, 0) . 'th';
    }
    
    static function scoreToString($score, $workout) {
        if($workout == 1) {
            if($score >= 40) {
                $score -= 40;
                if($score >= 30) {
                    $score -= 30;
                    if($score >= 30) {
                        $score -= 30;
                        if($score >= 30) {
                            $score -= 30;
                            if($score >= 20) {
                                $score -= 20;
                                if($score >= 30) {
                                    $score -= 30;
                                    if($score >= 10) {
                                        $score -= 10;
                                        return "40 burpees, 30 snatches, 30 burpees, 30 snatches, 20 burpees, 30 snatches, 10 burpees and ".$score." snatches";
                                    }
                                    else {
                                        return "40 burpees, 30 snatches, 30 burpees, 30 snatches, 20 burpees, 30 snatches, and ".$score. " burpees";
                                    }
                                }
                                else {
                                    return "40 burpees, 30 snatches, 30 burpees, 30 snatches, 20 burpees, and ".$score." snatches";
                                }
                            }
                            else {
                                return "40 burpees, 30 snatches, 30 burpees, 30 snatches, and ".$score." burpees";
                            }
                        }
                        else {
                            return "40 burpees, 30 snatches, 30 burpees, and ".$score." snatches";
                        }
                    }
                    else {
                        return "40 burpees, 30 snatches, and ".$score." burpees";
                    }
                }
                else {
                    return "40 burpees and ".$score." snatches";
                }
            }
            else {
                return $score ." burpees";
            }
        }
        else if($workout == 2) {
            $rounds = (int)($score / 30);
            $score = $score - $rounds * 30;
            if($score >= 5) {
                $score -= 5;
                if($score >= 10) {
                    $score -= 10;
                    return ($rounds > 0 ? $rounds . " rounds and " : "") . "5 shoulder to overhead, 10 deadlifts, and ".$score." box jumps";
                }
                else {
                    return ($rounds > 0 ? $rounds . " rounds and " : "") . "5 shoulder to overhead and " . $score . " deadlifts";
                }
            }
            else {
                if($score) {
                    return ($rounds > 0 ? $rounds . " rounds and " : "") . $score . " shoulder to overhead";    
                }
                else {
                    return ($rounds > 0 ? $rounds . " rounds " : "");
                }
            }
        }
        else if($workout == 3) {
            if($score > 150) {
                $score -= 150;
                if($score > 90) {
                    $score -= 90;
                    if($score > 30) {
                        $score -= 30;
                        if($score > 150) {
                            return "One full round (150/90/30), 150 wall balls and ".$score." double unders";
                        }
                        else {
                            return "One full round (150/90/30) and ".$score." wall balls";
                        }
                    }
                    else {
                        return "150 wall balls, 90 double unders, and ".$score." muscle-ups";
                    }
                }
                else {
                    return "150 wall balls and ". $score ." double unders";
                }
            }
            else {
                return $score. " wall balls";
            }
        }
        else if($workout == 4) {
            $reps = array(6, 18, 36, 60, 90, 126, 168, 216);
            for($i = count($reps) - 1; $i >= 0; $i--) {
                if($reps[$i] <= $score) {
                    $result = ($i+1) . " rounds";
                    $score -= $reps[$i];
                    $first = 3 * ($i + 2);
                    if($score > $first) {
                        $result .= ", ".$first." clean and jerks and ".($score-$first)." toe to bar";
                    }
                    else {
                        $result .= ", ".$score." clean and jerks";
                    }

                    return $result;
                }
            }
        }

        return "";
    }

    static function truncate($description, $max_length, $slice = false) {
        $order = array("\r\n", "\n", "\r");
        $description = str_replace($order, " ", $description);
        if(strlen($description) > $max_length) {
            $temp = substr($description, 0, $max_length-3);
            if(!$slice) {
                if(ctype_graph($description{$max_length-3})) {
                    for($i = $max_length-3; $i < strlen($description) && $i < $max_length+20; $i++) {
                        if(ctype_graph($description{$i})) {
                            $temp .= $description{$i};
                        }
                        else break;
                    }
                }
            }

            $description = $temp."...";
        }

        return $description;
    }
}
?>
