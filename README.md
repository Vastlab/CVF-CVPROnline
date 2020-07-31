# CVF-CVPROnline
Much of this is standard wordpress with a bunch of plugins but all the onese we used are here so we could setup new servers with git pull

most code in the subtheme  wp-content/themes/cvf-twentyseventeen-child/  but a few of the list-templates were customized in their directories since they did not support proper theme customization, but with copies in the theme.  E.g. the plugins/the-events-calendar-templates-and-shortcode/templates/list-template.php



Very rough code being developed by T. Boult and students of the VAST lab  to support CVPR2020 online.
Based on wordpress with various plugins (with their own licenses).
All the Boult/CVF code is BSD-3 license, and should cite.
This code is currently very  rough and not meant for easy reading but is public incase you want to use it.


The code presumes a particular directory structure with
CVPR20/XXX
where XXX is either CVPR20 (for main conference papers) or the 3didit code for workshop/tutorials e.g.
   ls CVPR20
yields
   CVPR20	T07  T13  T16  T22  T25  W01  W05  W09	W14  W17  W20  W23  W26  W29  W32  W35	W42  W46  W50  W53  W57  W61  W64  W67	W70
   T03	T10  T14  T19  T23  T26  W02  W06  W11	W15  W18  W21  W24  W27  W30  W33  W40	W43  W47  W51  W54  W58  W62  W65  W68
   T05	T12  T15  T20  T24  T29  W03  W07  W12	W16  W19  W22  W25  W28  W31  W34  W41	W45  W49  W52  W56  W59  W63  W66  W69

In each directory there is a directory for each paper e.g.
        ls CVPR20/W03
yields
        1  10  11  12  13  14  15  16  17  18  19  2  3  4  5  6  7  8	9
and in each directory is a structured set of files 
    ls CVPR20/W03/10
yields
        10-keywords.txt  10-oral.mp4  10-talk.pdf  10-teaser.gif  10-teaser.txt

The file nameing follwed the instructions for the conference which follow.  Note the upload was separate code.
 Instructions were:
 You will submit a zip file containing multiple types of files: pdf, gif, txt and mp4. Upload all your files in one zip file. If you don’t know how to make a zip, consult this guide. The zip file should NOT contain directories or subdirectories, just the appropriately named files. Inappropriate naming will cause your portion of the virtual conference to fail to work — and it may not be detected until the conference is live and if it is the author’s error, it may not be corrected.  Name your zip file $PAPERID.zip where $PAPERID is your unique CVPR paper id (from submission).  There is no $ in the filename, $PAPERID is  a variable to be expanded. For example, for CVPR paper ID #12, please use “12-oral.mp4” as the file name for the oral video, and so on. No leading zeros.

Main Conference Poster only papers submit 5 files:

$PAPERID-1min.mp4
$PAPERID-slides.pdf (Slides from 1min video)
$PAPERID-teaser.gif
$PAPERID-teaser.txt
$PAPERID-keywords.txt
Main Conference Oral +poster papers submit 7 files:

$PAPERID-oral.mp4
$PAPERID-talk.pdf
$PAPERID-1min.mp4
$PAPERID-slides.pdf (Slides from 1min video)
$PAPERID-teaser.gif
$PAPERID-teaser.txt
$PAPERID-keywords.txt
Workshop Oral paper  or Tutorial submit 5 files:

$PAPERID-oral.mp4 (Video for “Oral” talk)
$PAPERID-talk.pdf (slides from oral talk)
$PAPERID-teaser.gif
$PAPERID-teaser.txt
$PAPERID-keywords.txt
Workshop Poster paper submit 5 files:

$PAPERID-poster.mp4
$PAPERID-slides.pdf (Slides from poster video)
$PAPERID-teaser.gif
$PAPERID-teaser.txt
$PAPERID-keywords.txt
keywords.txt file  —Keywords are now requested for indexing papers.  Please include up to 10 keywords, comma-separated, in a text file (may be truncated for display, so please sort in descending order of importance).
teaser.txt file — 150 character teaser text; Count  includes white space.

teaser.gif file  –One 512×512 teaser image with main result.  It can be generated with just about any image generation tool (photoshop, gimp) or even online tools.

slides.pdf file — slides from your video presentation.  16:9 aspect ratio.  PDF files should be  PDF compliant with all fonts embedded (just like your camera ready CVPR submitted papers were).

1min.mp4 file  — 1 minute ‘advertisement/spotlight’ video.  See video guidelines below.

talk.pdf file —  (Oral papers only) slides that go with the video. PDF files should be  PDF compliant with all fonts embedded (just like your camera ready CVPR submitted papers were).

oral.mp4 file  —  (Oral papers only) video of your talk.   See video guidelines below.   Workshops- follow time restrictions from your organizers.

poster.mp4 file  —  (workshop poster only papers) video of your talk.   See video guidelines below.   Workshops- follow time restrictions from your organizers.

The Zip should include ONLY the required files, not a directory containing the files and not any extra files such as from editing.

Please ensure your video follows all of the following guidelines before uploading.

Videos can be in only one format: MP4 video 1920x1080p in H264 compression. This will enable us to load all talks into the online system for remote presentations. Videos that do not fit this resolution will be resized for the presentation or may be discarded.

All videos should have narration. You may get someone else to do your voice over. Human narration is preferred but text-to-speech (TTS), is allowed if it makes the video easier to understand. There are some bad TTS and some better ones. If your narration is bad, people are more likely to skip your video.

When presenting, introduce yourself. There will be no time allotted for “questions” during the video session. As usual, any projected Text/Math should use at least 24 point font (and ideally should be >32pt) as smaller fonts will not be readable on small mobile screens.

Video/Slide Presentation Formatting

Talk slides need to be converted to video.

The video should be no more than the allotted time. It should start with a title slide. The video MUST be named as in the upload page and should be a Mpeg4 with 1920×1080 resolution, with H264 encoding.  (H264 is the default codec for many MP4 encoders, but not all). It should NOT be an AVI file or a MOV but proper MP4 — and you cannot just rename it to convert it).  You should record your narration.  You are free to use picture-in-a-picture.

You might use Youtube or FFMPEG to convert formats.

If you prepare your presentation using PowerPoint, you can time your slides and save the presentation as a WMV video directly from PowerPoint. Some instructions on how to do this can be found here: https://support.office.com/en-us/article/Turn-your-presentation-into-a-video-c140551f-cb37-4818-b5d4-3e30815c3e83

You will then need to convert the WMV video into MP4 (e.g. via Youtube or FFMPEG).

Alternatively, there are many free screen capture programs that directly produce proper MP4:

VLC- (http://www.videolan.org/vlc/index.html)which works on all platforms.
OBS- https://obsproject.com/
Tinytake- (http://tinytake.com/) for Windows.
For Mac there is the built-in QuickTime, which will need to be exported as MP4 because the QuickTime default is MOV).
For Mac users using keynote
Keynote now. Fabian Mentzer says You can do Play > Record Slideshow…, then record it. This seems to just record timestamps. But then you do File > Export to Movie, select “Slideshow Recording”, Custom resolution (1920×1080). This puts a H264-encoded video in an m4v, which can be turned into a mp4 with ffmpeg as below
If you have an mp4 or m4v file after your encoding you can upload that directly. If you only have video recorders producing other formats, e.g. avi or wmf , Youtube may be able to convert for you (upload then download) or convert using FFMPEG ( https://ffmpeg.org/

ffmpeg -i source_video.avi -acodec aac -c:v libx264 -crf 16 -s 1920×1080 -t 4:55 -preset slow -s 1920×1080 PAPERNUMBER.mp4
One can even use FFMPEG to create a video from slides, but screen recording is far easier.
If using FFMPEG or other tools please make sure the frame rate is 30fps.
After upload, you will receive an email    If you upload a second time it will simply overwrite your original file, so if you want to update the file just upload a second time. But please don’t upload again and again as it takes a lot of resources.

If you do not see an email after your submission,  check your SPAM folder. 