<h2>PAGES AND COMMENTS PLUGIN.</h2>

<h3 style="margin-bottom: 1px;">Test Web:</h3>
<ul>
<li><a href="http://cumbe.260mb.org/">Cumbe's web</a></li>
</ul>

<h3 style="margin-bottom: 1px;">FEATURES:</h3>
<ul>
<li> Works with GS31 and GS32</li>
<li> Include comments in pages.</li>
<li> From Admin > pages, settings system.</li>
<li> Backup system.</li>
<li> CSS system. CSS file is in /plugins/pages_comments/pc.css.</li>
<li> Works with fancy url on and off.</li>
<li> Support bbcode</li>
<li> Send email to notify using standar function or class phpmailer.</li>
<li> Join to plugin frontend user plugin is possible have system of comment with user registered.</li>
<li> Shows recent comments in settings.</li>
<li> Customize the tittle of form for each page.</li>
<li> Moderated comments if you want.</li>
</ul>


<div style="font-family: times New Roman; font-size: 15px;">
<h3 style="margin-bottom: 1px;">INSTRUCTIONS:</h3>
&nbsp;&nbsp;&nbsp;<p style="margin-bottom: 0px;">Unzip in folder plugins.<br />
&nbsp;&nbsp;&nbsp;<p style="margin-bottom: 0px;">a) From admin > pages > pages&comments button: <b>configuration of settings of plugin,</b> choose lang, email of notification, format date publication, default value for system comments...(view button Settings)</p>
&nbsp;&nbsp;&nbsp;<p style="margin-bottom: 0px;">b) To have pages created.</p>
&nbsp;&nbsp;&nbsp;<p style="margin-bottom: 0px;">c)<b> if you want COMMENTS IN PAGE</b><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;You can choose in that pages want comments from 'Add comment to pages' >> Add. (view button Add Comments to page)<br/ >

&nbsp;&nbsp;&nbsp;&nbsp;Comments are created in data/other/pages_comments, 1 file for page: name_of_page.log.<br />
&nbsp;&nbsp;&nbsp;&nbsp;If a page changes the url, too is changed the log of comments, and the url in data/other/pages_comments/pc_manager.xml.</p>


&nbsp;&nbsp;&nbsp;<p style="margin-bottom: 0px;">g) Data are saved in:
<ul>
<li>The general configuration is saved in data/other/pages_comments/pc_settgs.xml</li>
<li>The settings of pages are saved in data/other/pages_comments/pc_manager.xml</li>
<li>The setting of groups are saved in data/other/pages_comments/pc_group.xml</li>
<li>All comments are saved in data/other/pages_comments/pc_lastcom.xml</li>
</ul>
</p>
<br />


<h3 style="margin-bottom: 1px;">BUTTONS:</h3>
<ol>
<li>RECENTS COMMENTS<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Here, we can to see the last comments of all pages. From here, you can to remove a comment.<br /><br />
</li>
<li>PAGES WITH COMMENTS<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To see the pages that has system of comments. You can:<br /><br />
  <ul>
      <li type="circle">Edit pages, click in page title , to choose if you want show comments, to use captcha, emoticons,...</li>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;In comments, you can put emoticons. The emoticons are in /plugins/pages_comments/img_emots . If you want add more emoticons, only you have to paste *.gif in this folder. If you want delete some emoticons, only you have to delete it from folder.<br />

      <li type="circle">Comments, to see comments of each page. If a page has comments, will appear number of comments; click in one you can see, remove, moderate the comments, and create a backup of comments log.</li>

      <li type="circle">Preview system comments from Page. Click in '#', you can preview page + comments.</li>

      <li type="circle">Delete system comments from Page. Click in 'x', you can remove only system comments, or page + comments of this page.</li>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;It NEVER remove the page of 'PAGES'.

     </li>
  </ul> 
</li>
<br />
<li>ADD COMMENTS TO PAGES<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Will see all pages created that has not system comment;. We can to do:
     <ul>
         <li>to add the system comments, click in Add.</li>
         <li>Edit options, to choice if you want to use captcha, emoticons,...</li>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Is possible works joint Frontend user plugin. Of this way you can have a system of users that can write comments if they are logged.<br />
     </ul>
</li>
<li>BACKUPs<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Will see the list of backup of logs (comments). These backups are created in View Comments (number of comments if there are comments).<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Every time that a log is created, the name of backup is: name of log + date + time.bak. It is possible recover this log, and remove it. When a log is recovered, always is done a copy of current log.<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The backups are saved in data/other/news_comments/news_comments_bak.<br /><br />
</li>

<li>SETTINGS or Configuration general, you have to choose:<br />
    <ul>
        <li>Language to use.</li>
        <li>Email: email that received notices from comments...</li>
        <li>Post date format: there 5 formats to date publication in comments.</li>
        <li>Receive notification by email: 3 options, never, to use standar function, or to use class phpmailer.</li>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If you need to use class phpmailer then you must complete the file /pages_comments/check.php with correct data in lines +- 249 to 273, and comment or uncomment the lines that need.
        <li>Default values to system comments. </li>
        <li>Works with Front end user: yes or no.</li>
        <li>If user can delete comments if main comment has replies.</li>
        <li>Number of comments for recents posts.</li>
    </ul>
</li>

<li>HELP: here there is a summary and instructions of like works the plugin.</li><br />

<li>BBCODE<br />
Supports:
   <ul>
      <li>[b]Bold[/b]</li>
      <li>Italic[/i]</li>
      <li>Underline[/u]</li>
      <li>[img]url image[/img]</li>
      <li>[color=colourthatyouwant]text[/color]</li>
      <li>Linking with no link title: [url]link to url[/url]</li>
      <li>Linking to a named site: [url=urlthatyouwant]title[/url]</li>
   </ul>
</li>
</ol>
I'm sorry for my english.

Regards.
</div>

