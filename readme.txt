This snippet switches between different languages

To execute Multilingual you have to 
1) Enable Page Languages
2) Page Level Limit higher 1 (folder level)
3) Required template modifications
    <?php if(function_exists('language_menu')) { language_menu('gif|png'); } ?>
    
    you can show gif or png flags
    following container will be created, style it with your template css
    <div id="langmenu">
    <a title="Deutsch" href="/pages/de/startseite.php" class="current">
    <span><img title="Deutsch" alt="Deutsch" src="/modules/mod_multilingual/flags/de.gif"></span></a>
    <a title="English" href="/pages/en/home.php" class="default">
    <span><img title="English" alt="English" src="/modules/mod_multilingual/flags/en.gif"></span></a>
    </div>

4) Setting up the page structure
   http://help.websitebaker.org/en/help/designerguide/multilingualwebsites.php

   Language selection via the intro page no more needed

5) After installing the Multilingual modul
   In Modify Page Settings you find an extra field Pagecode with a directory listing with pages from default languages.
   The folder level is limited by the value from Page Level Limit

   a) Create your pages for the default language
   b) Create your pages for the 2nd language
   c) select a page in the 2nd language area and call Modify Page Settings
   d) in field Pagecode select the default language page and save

