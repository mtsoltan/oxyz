INSERT IGNORE INTO products VALUES(5,1,
    'ar=تصميم ميكانيكي هندسي وخردوات\n'
    'en=Mechanical and Gadget Design\n',
    'ar='
    'بكل بساطة يمكنك ارسال صورة او ابعاد او تصور لأي '
    'منتج أيا كان تصميم ميكانيكي او ديكور او كماليات وسوف '
    'نقوم بتصميمه لك. نحن أيضا نوفر لك وسائل وإرشادات '
    'لتصنيع منتجك بكفاءة بخبرتنا السابقة في عمليات التصنيع '
    'المختلفة. '
    '\n'
    'en='
    'You simply send us a photo or dimensions for any product, be it a mechanical product, '
    'a decorative product, or a gadget, and we '
    'will design it for you. We also offer you the ways to manufacture this product in '
    'an efficient way using our experience in manucature procedures. '
    '\n',
  '/static/images/services/5.jpg',1,0,1,'',UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,1,
    'ar=الصورة\n'
    'en=Picture\n',
    'ar=برجاء رفع صورة للتصميم المراد تنفيذه او ملف مضغوط يحتوي علي اكثر من صورة ان وجدت\n'
    'en=Please upload the picture of the product you want designed, or a compressed file (zip, rar) containing the images if you wish to provide multiple images.\n',
    '__form::\n' 'name=file\n' 'type=file\n' 'id=file\n' 'required=1\n',
  3,5,1,0,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,2,
    'ar=نوع التصميم\n'
    'en=Design Type\n',
    'ar=برجاء توضيح نوع التصميم المراد تنفيذه.\n'
    'en=Please select the desired design type from the following options.\n',
    '[ar]\n'
    '0=تصميم هندسي ميكانيكي\n'
    '1=تصميم ديكورات وزخارف\n'
    '2=افكار منتجات استهالكية اخري\n'
    '[en]\n'
    '0=Mechanical Engineering Design\n'
    '1=Decor and Decorative Motif Design\n'
    '2=Other Consumer Product Ideas\n',
  3,5,1,1,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,127,
    'ar=وصف للمنتج المراد تصميمه\n'
    'en=Product Description.\n',
    'ar=برجاء ادارج وصف للمنتج و الخامة المرات تصنيعه منها والاستخدامات وسيتم التواصل مع سيادتكم هاتفيا للتاكيد اكثر او بزيارة سيادتكم لمكتبنا\n'
    'en=Please describe the product you want designed, including information about the dimensions, material, and uses of the product.\n',
    '__form::\n' 'name=notes\n' 'type=textarea\n' 'id=notes\n' 'maxlength=4095\n',
  3,5,1,0,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,126,
    'ar=\n'
    'en=Allowed mechanical design upload extensions\n',
    'ar=\n'
    'en=\n',
    '0=jpg\n'
    '1=jpeg\n'
    '2=png\n'
    '3=rar\n'
    '4=rar\n'
    '5=zip\n',
  3,5,0,1,UNIX_TIMESTAMP(),0);
