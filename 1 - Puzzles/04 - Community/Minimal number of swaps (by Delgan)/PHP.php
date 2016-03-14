<? //Minified version
for($b=$i=0,$n=fgets(STDIN),$c=explode(" ",fgets(STDIN));$i<$n;$b+=$c[$i++]);
for($d=$i=0;$i<$b;$d+=!$c[$i++]);
echo $d;