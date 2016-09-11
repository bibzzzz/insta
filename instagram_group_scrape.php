<?php

//Supply a username
$accounts = array("wessavedlatin","bibzzzz"); 
mkdir('/Users/amydonaldson/Documents/Habib/dev/instagram/group_analysis', 0755, true);

//returns a big old hunk of JSON from a non-private IG account page.
function scrape_insta($username) {
	$insta_source = file_get_contents('http://instagram.com/'.$username);
	$shards = explode('window._sharedData = ', $insta_source);
	$insta_json = explode(';</script>', $shards[1]); 
	$insta_array = json_decode($insta_json[0], TRUE);
	return $insta_array;
}

function pixelate($image, $output, $pixelate_x = 20, $pixelate_y = 20)
{
    // check if the input file exists
    if(!file_exists($image))
        echo 'File "'. $image .'" not found';

    // get the input file extension and create a GD resource from it
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    if($ext == "jpg" || $ext == "jpeg")
        $img = imagecreatefromjpeg($image);
    elseif($ext == "png")
        $img = imagecreatefrompng($image);
    elseif($ext == "gif")
        $img = imagecreatefromgif($image);
    else
        echo 'Unsupported file extension';

    // now we have the image loaded up and ready for the effect to be applied
    // get the image size
    $size = getimagesize($image);
    $height = $size[1];
    $width = $size[0];

    // start from the top-left pixel and keep looping until we have the desired effect
    for($y = 0;$y < $height;$y += $pixelate_y+1)
    {

        for($x = 0;$x < $width;$x += $pixelate_x+1)
        {
            // get the color for current pixel
            $rgb = imagecolorsforindex($img, imagecolorat($img, $x, $y));

            // get the closest color from palette
            $color = imagecolorclosest($img, $rgb['red'], $rgb['green'], $rgb['blue']);
            imagefilledrectangle($img, $x, $y, $x+$pixelate_x, $y+$pixelate_y, $color);

        }       
    }

    // save the image
    $output_name = $output .'_pix.jpg';

    imagejpeg($img, $output_name);
    imagedestroy($img); 
}


$user_count = count($accounts);

for($j = 0, $size_a = $user_count; $j < $size_a; ++$j) {
    //Do the deed
    $results_array = scrape_insta($accounts[$j]);
    $photo_count = count($results_array['entry_data']['ProfilePage'][0]['user']['media']['nodes']);
    for($i = 0, $size = $photo_count; $i < $size; ++$i) {
        $collection[$i] = $results_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'][$i]['display_src'];
        copy($results_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'][$i]['display_src'], '/Users/amydonaldson/Documents/Habib/dev/instagram/group_analysis/img_collection_'.$i.'_'.$accounts[$j].'.jpg');
        echo $results_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'][$i]['display_src'];
        pixelate('/Users/amydonaldson/Documents/Habib/dev/instagram/group_analysis/img_collection_'.$i.'_'.$accounts[$j].'.jpg', '/Users/amydonaldson/Documents/Habib/dev/instagram/group_analysis/img_collection_'.$i.'_'.$accounts[$j]);
        echo "\r\n";
    }
}

// echo $insta_source;

// echo 'Latest Photo:<br/>';
// echo '<a href="http://instagram.com/p/'.$latest_array['code'].'"><img src="'.$latest_array['display_src'].'"></a></br>';
// echo 'Likes: '.$latest_array['likes']['count'].' - Comments: '.$latest_array['comments']['count'].'<br/>';

?>
*/