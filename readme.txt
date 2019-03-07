Thank you for reading. I hope this short text will help you know ad part of our 3156.cn project.

notice: gg means ad.

1. You can see templates to know how front-end developers call Ad api to show ads, example: www\app\templates\tpl1\main.index.html, like this: {|gg('gid:2')|}, it will show all Ads belongs to group 

2.gg($cmd) is a common function in framework\Library\Service\com\blocks.func.php. I wrote how to use this api for front-end developers, but it is Chinese. If you want, I can translate it into English.

3. We used SlightPHP, it based on MVC. I wrote gg logic code in Service layer: framework\Library\Service\gg\position.class.php

4. The gg models are in here: \framework\Library\Model, folders starting with gg. (I told to my colleague that all related ad models should put in one gg folder)

5. I worte many comments in my code, and you can search baoxianjian,or baoge, to see which parts were coded by I.