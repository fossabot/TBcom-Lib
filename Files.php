<?php
/* TBcom-Lib Files
 *
 *     \TBcom\MyFile
 *
 * Copyright (c) 2018-2020 Tanner Babcock.
 * This software is licensed under the terms of the MIT License. See LICENSE for details.
 */
namespace TBcom;

///    \TBcom\MyFile
///
class MyFile {
	public $select;
	public $path;
	public $title;

	public function __construct($s, $p, $t) {
		$this->select = $s;
		$this->path = $p;
		$this->title = $t;
	}
}
/*
$MyFiles[] = new MyFile("bashrc", "babkock-dotfiles/bashrc", "Dotfiles / .bashrc");
$MyFiles[] = new MyFile("bash_aliases", "babkock-dotfiles/bash_aliases", "Dotfiles / .bash_aliases");
$MyFiles[] = new MyFile("config.conf", "babkock-dotfiles/neofetch/config.conf", "Dotfiles / neofetch config.conf");
$MyFiles[] = new MyFile("uc.firefox", "babkock-dotfiles/chrome/userChrome.firefox.css", "Dotfiles / userChrome.firefox.css");
$MyFiles[] = new MyFile("uco.firefox", "babkock-dotfiles/chrome/userContent.firefox.css", "Dotfiles / userContent.firefox.css");
$MyFiles[] = new MyFile("uc.thunderbird", "babkock-dotfiles/chrome/userChrome.thunderbird.css", "Dotfiles / userChrome.thunderbird.css");
$MyFiles[] = new MyFile("fetch.sh", "babkock-dotfiles/fetch.sh", "Dotfiles / fetch.sh");

$MyFiles[] = new MyFile("bspwmrc", "babkock-dotfiles/bspwm/bspwmrc", "Dotfiles / bspwm/bspwmrc");
$MyFiles[] = new MyFile("sxhkd", "babkock-dotfiles/sxhkd/sxhkdrc", "Dotfiles / sxhkd/sxhkdrc");
$MyFiles[] = new MyFile("st", "babkock-dotfiles/st/st_config.h", "Dotfiles / st/st_config.h");
$MyFiles[] = new MyFile("st3", "babkock-dotfiles/st/st3_config.h", "Dotfiles / st/st3_config.h");

$MyFiles[] = new MyFile("i3config", "babkock-dotfiles/i3/config", "Dotfiles / i3/config");
$MyFiles[] = new MyFile("polybar", "babkock-dotfiles/polybar/config", "Dotfiles / polybar/config");
$MyFiles[] = new MyFile("nanorc", "babkock-dotfiles/nanorc", "Dotfiles / .nanorc");
$MyFiles[] = new MyFile("ncmpcpp", "babkock-dotfiles/ncmpcpp/config", "Dotfiles / ncmpcpp config");
$MyFiles[] = new MyFile("rc.conf", "babkock-dotfiles/ranger/rc.conf", "Dotfiles / ranger rc.conf");
$MyFiles[] = new MyFile("rc.lua", "babkock-dotfiles/awesome/rc.lua", "Dotfiles / Awesome rc.lua");
$MyFiles[] = new MyFile("theme.lua", "babkock-dotfiles/awesome/themes/babkock/theme.lua", "Dotfiles / Awesome theme.lua");
$MyFiles[] = new MyFile("xbindkeysrc", "babkock-dotfiles/xbindkeysrc", "Dotfiles / .xbindkeysrc");
$MyFiles[] = new MyFile("xresources.awe", "babkock-dotfiles/Xresources.awe", "Dotfiles / .Xresources.awe");
$MyFiles[] = new MyFile("xresources.i3", "babkock-dotfiles/Xresources.i3", "Dotfiles / .Xresources.i3");
$MyFiles[] = new MyFile("battery", "babkock-dotfiles/bin/battery", "Dotfiles / battery");
$MyFiles[] = new MyFile("start", "babkock-dotfiles/bin/start", "Dotfiles / start");
$MyFiles[] = new MyFile("volume", "babkock-dotfiles/bin/volume", "Dotfiles / volume");
$MyFiles[] = new MyFile("dwm.config", "babkock-dotfiles/dwm/config.h", "Dotfiles / DWM config.h");
$MyFiles[] = new MyFile("dwm.dwm", "babkock-dotfiles/dwm/dwm.c", "Dotfiles / DWM dwm.c");
$MyFiles[] = new MyFile("sl.config", "babkock-dotfiles/slstatus/config.h", "Dotfiles / SlStatus config.h");
 */

$MyFiles[] = new MyFile("tm_draw.c", "tonematrix-0.92/src/draw.c", "ToneMatrix / draw.c");
$MyFiles[] = new MyFile("tm_draw.h", "tonematrix-0.92/src/draw.h", "ToneMatrix / draw.h");
$MyFiles[] = new MyFile("tm_file.c", "tonematrix-0.92/src/file.c", "ToneMatrix / file.c");
$MyFiles[] = new MyFile("tm_grid.c", "tonematrix-0.92/src/grid.c", "ToneMatrix / grid.c");
$MyFiles[] = new MyFile("tm_main.c", "tonematrix-0.92/src/main.c", "ToneMatrix / main.c");
$MyFiles[] = new MyFile("tm_main.h", "tonematrix-0.92/src/main.h", "ToneMatrix / main.h");
$MyFiles[] = new MyFile("tm_Makefile", "tonematrix-0.92/src/Makefile", "ToneMatrix / Makefile");
$MyFiles[] = new MyFile("tm_menu.c", "tonematrix-0.92/src/menu.c", "ToneMatrix / menu.c");
$MyFiles[] = new MyFile("tm_menu.h", "tonematrix-0.92/src/menu.h", "ToneMatrix / menu.h");
$MyFiles[] = new MyFile("tm_misc.c", "tonematrix-0.92/src/misc.c", "ToneMatrix / misc.c");
$MyFiles[] = new MyFile("tm_rhythm.c", "tonematrix-0.92/src/rhythm.c", "ToneMatrix / rhythm.c");
$MyFiles[] = new MyFile("tm_sound.c", "tonematrix-0.92/src/sound.c", "ToneMatrix / sound.c");
$MyFiles[] = new MyFile("bb_colors.c", "beatbox-1.6/src/colors.c", "Beatbox / colors.c");
$MyFiles[] = new MyFile("bb_files.c", "beatbox-1.6/src/files.c", "Beatbox / files.c");
$MyFiles[] = new MyFile("bb_main.c", "beatbox-1.6/src/main.c", "Beatbox / main.c");
$MyFiles[] = new MyFile("bb_main.h", "beatbox-1.6/src/main.h", "Beatbox / main.h");
$MyFiles[] = new MyFile("bb_Makefile", "beatbox-1.6/src/Makefile", "Beatbox / Makefile");
$MyFiles[] = new MyFile("bb_pause.c", "beatbox-1.6/src/pause.c", "Beatbox / pause.c");
$MyFiles[] = new MyFile("bb_sounds.c", "beatbox-1.6/src/sounds.c", "Beatbox / sounds.c");
$MyFiles[] = new MyFile("sp_hsv2rgb.c", "spectrum/hsv2rgb.c", "Spectrum / hsv2rgb.c");
$MyFiles[] = new MyFile("sp_main.c", "spectrum/main.c", "Spectrum / main.c");
$MyFiles[] = new MyFile("sp_Makefile", "spectrum/Makefile", "Spectrum / Makefile");
$MyFiles[] = new MyFile("tp_framebuffer.c", "tripprog-0.9/framebuffer.c", "Tripprog / framebuffer.c");
$MyFiles[] = new MyFile("tp_framebuffer.h", "tripprog-0.9/framebuffer.h", "Tripprog / framebuffer.h");
$MyFiles[] = new MyFile("tp_graphics.c", "tripprog-0.9/graphics.c", "Tripprog / graphics.c");
$MyFiles[] = new MyFile("tp_graphics.h", "tripprog-0.9/graphics.h", "Tripprog / graphics.h");
$MyFiles[] = new MyFile("tp_main.c", "tripprog-0.9/main.c", "Tripprog / main.c");
$MyFiles[] = new MyFile("tp_main.h", "tripprog-0.9/main.h", "Tripprog / main.h");

