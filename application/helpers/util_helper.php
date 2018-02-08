<?php

    function util_printArray($pArray){

        foreach($pArray as $keyArray => $itemArray){

            echo "<br>---------------------------------------<br>";
            echo "<h4>Key/Index => ".$keyArray."</h4>";

            foreach($itemArray as $keyItemArray => $itemArray){

                echo "----<b>[".$keyItemArray."]</b> => ";

                if(!is_array($itemArray)){
                    echo $itemArray;
                }

                if(is_array($itemArray)){
                    
                    foreach($itemArray as $keyItemSubArray=> $itemSubArray){

                        echo "<br>-------- <b>[".$keyItemSubArray."]</b> => ";

                        if(!is_array($itemSubArray)){
                            echo $itemSubArray;
                        }

                        if(is_array($itemSubArray)){
                            foreach($itemSubArray as $keyItemSub2Array=> $itemSub2Array){

                                echo "<br>------------ <b>[".$keyItemSub2Array."]</b> => ".$itemSub2Array;
                            }
                        }
                        
                    }
                }

                echo "<br>";
            }

        }

    }