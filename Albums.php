<?php
/* LIB/ALBUMS
 *
 * Copyright (c) 2019 Tanner Babcock. All rights reserved.
*/
namespace TBcom;

class Album {
	public $title;
	public $description;
	public $keywords;
	public $rdate;

	public function __construct($t, $d, $k, $r) {
		$this->title = $t;
		$this->description = $d;
		$this->keywords = $k;
		$this->rdate = $r;
	}
}

$Albums = [];
$Albums[] = null;
$Albums[] = new Album("Culture Chester - Real Men Don't Rape", "Real Men Don't Rape is an album by Culture Chester. Baseball Factory, The Grass Is Green, Mark Train, She Is Not Your Beautiful House. February 26, 2015. Listen, purchase, browse.", "culture chester, culture, chester, real men don't rape, noise rock, no wave, synth punk, newt grundy, henry knollenberg,", "February 26, 2015");
$Albums[] = new Album("Erases Eraser - Toy and a Half", "An EP by Erases Eraser. Out in the Mold, Dopamine, Mace Gravel, Addicted to Talentless Crack, Marketing Ploys. March 26, 2015. Listen, purchase, browse.", "erases eraser, erases, eraser, toy and a half, toys, toy, IDM, glitchadelic, psychedelic, microwave, toasterwave,", "March 26, 2015");
$Albums[] = new Album("Erases Eraser - Domestic Violence", "Domestic Violence is an album by Erases Eraser. The Man of the House, Broken Toys, Spaghetti Carbonara, 'Real' Psychedelic, Boogers and Scabs. May 6, 2015. Listen, purchase, browse.", "erases eraser, domestic violence, domestic, violence, IDM, glitchadelic, man of the house, spaghetti, carbonara, psychedelic, microwave, insane, abrasive, toasterwave,", "May 6, 2015");
$Albums[] = new Album("Erases Eraser - e", "'e' is an album by Erases Eraser. Spiders, Mommy, The Alcoholic, Sky Rocket, Have a Nice Trip!. June 30, 2015. Listen, purchase, browse.", "erases eraser, erases, eraser, e, E, gray album, noise rock, no wave, noise, post-rock, mommy, alcoholic, spiders,", "June 30, 2015");
$Albums[] = new Album("Erases Eraser - r", "'r' is an album by Erases Eraser. Honey Nut Cheerios, Lucky Charms, Fruity Pebbles, Cinnamon Toast Crunch, Raisin Bran. August 24, 2015. Listen, purchase, browse.", "erases eraser, erases, eraser, r, R, red album, IDM, glitchadelic, psychedelic, breakbeat, microwave, toasterwave, cinnamon, fruity pebbles, raisin bran, lucky charms,", "August 24, 2015");
$Albums[] = new Album("Tantrum Throwers - Throw Tantrums", "Throw Tantrums is an album by Tantrum Throwers. Creepy Clock, Starving, No-Head Bobbin, Sunday Afternoon. November 6, 2015. Listen, purchase, browse.", "tantrum throwers, tantrum, throwers, throw tantrums, throw, tantrums, noise rock, no wave, shoegaze, post-punk, luke belknap, starving, creepy clock, toasterwave,", "November 6, 2015");
$Albums[] = new Album("Erases Eraser - s", "'s' is an album by Erases Eraser. The Heroin Addict, Crosstalk, Zeroes and Ones, Nails on a Chalkboard, Something in Hebrew Text. June 24, 2016. Listen, purchase, browse.", "erases eraser, erases, eraser, s, S, blue album, IDM, glitchadelic, psychedelic, breakbeat, microwave, toasterwave,", "June 24, 2016");
$Albums[] = new Album("Tantrum Throwers - Futrepaction", "Futrepaction is an album by Tantrum Throwers. Dissociative Retinal Device, Amplitude Modulaion, Concept of a, The Big Ridiculous. August 5, 2016. Listen, purchase, browse.", "tantrum throwers, tantrum, throwers, futrepaction, noise rock, free jazz, drone metal, post-rock, free improvisation, noise, luke belknap, kristin owens,", "August 5, 2016");
$Albums[] = new Album("Tantrum Throwers - A Circle of Strange", "A Circle of Strange is a live album by Tantrum Throwers. Void Consumes You, Impulse, Attention Deficit, Stereotypy, Panic. January 16, 2017. Listen, purchase, browse.", "tantrum throwers, tantrum, throwers, a circle of strange, circle, strange, noise rock, free jazz, drone metal, post-rock, free rock, free improvisation, noise, luke belknap, kristin owens,", "January 16, 2017");
$Albums[] = new Album("Erases Eraser - The Dead End", "The Dead End is an album by Erases Eraser. ?, @, #, &amp;, &perc;, !. June 6, 2017. Listen, purchase, browse.", "erases eraser, erases, eraser, the dead end, dead end, purple album, IDM, glitchadelic, psychedelic, ambient, experimental, microwave, toasterwave,", "June 6, 2017");
$Albums[] = new Album("tth - tth", "tth is an album by Tantrum Throwers. Here Come We, Coleslaw Blues, Close the Window When You Go to Bed, Drink Full Dusty. July 16, 2017. Listen, purchase, browse.", "tantrum throwers, tth, tantrum, throwers, noise rock, space rock, psychedelic rock, post-rock, free improvisation, experimental, luke belknap, kristin owens,", "July 16, 2017");
$Albums[] = new Album("Culture Chester - Outer Baseball", "Outer Baseball is an album by Culture Chester. Baseball Factory, The Grass Is Green, The Brothel Buffet, Clack Clack Clack. January 4, 2018. Listen, purchase, browse.", "culture chester, culture, chester, outer baseball, outer, baseball, noise rock, no wave, synth punk, experimental, newt grundy, henry knollenberg,", "January 3, 2018");
$Albums[] = new Album("Erases Eraser - Neuroses", "Neuroses is an album by Erases Eraser. ptsd, adhd, did, gad, ocd, bpd, ps, au. February 3, 2018. Listen, purchase, browse.", "erases eraser, erases, eraser, neuroses, orange album, orange, psychotic, illness, schizophrenia, IDM, glitchadelic, harsh noise, insane, breakbeat,", "February 2, 2018");
