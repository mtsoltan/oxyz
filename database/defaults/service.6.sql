INSERT IGNORE INTO products VALUES(6,0,
    'ar=\n'
    'en=Electric Design\n',
    'ar='
    'نحن نقوم بتصميم أي دوائر الكترونية يمكن تنفيذها علي '
    'لوح الكترونية ذات وجه او وجهين، فقط أرسل لنا وصف '
    'الدائرة المراد تصميمها وأي بيانات لها علاقة بالتصنيع. '
    '\n'
    'en='
    'We offer to design any electronic circuit that can be feasibly implemented on a '
    'single / double sided FR4 PCB board. Just send us a description of what you want '
    'the product to do, and any data related to the production procedure, and '
    'we will have a look at it and design it for you. '
    '\n',
  '/static/images/services/6.jpg',1,0,1,'',UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,1,
    'ar=الصورة\n'
    'en=Picture\n',
    'ar=برجاء رفع صورة للتصميم المراد تنفيذه او ملف مضغوط يحتوي علي اكثر من صورة ان وجدت\n'
    'en=Please upload the picture of the product you want designed, or a compressed file (zip, rar) containing the images if you wish to provide multiple images.\n',
    '__form::\n' 'name=file\n' 'type=file\n' 'id=file\n' 'required=1\n',
  3,6,1,0,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,2,
    'ar=نوع التصميم\n'
    'en=Design Type\n',
    'ar=برجاء توضيح نوع التصميم المراد تنفيذه.\n'
    'en=Please select the desired design type from the following options.\n',
    '[ar]\n'
    '0=\n'
    '[en]\n'
    '0=\n',
  3,6,1,1,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,127,
    'ar=وصف للمنتج المراد تصميمه\n'
    'en=Product Description.\n',
    'ar=برجاء ادارج وصف للمنتج و الخامة المرات تصنيعه منها والاستخدامات وسيتم التواصل مع سيادتكم هاتفيا للتاكيد اكثر او بزيارة سيادتكم لمكتبنا\n'
    'en=Please describe the product you want designed, including information about the dimensions, material, and uses of the product.\n',
    '__form::\n' 'name=notes\n' 'type=textarea\n' 'id=notes\n' 'maxlength=4095\n',
  3,6,1,0,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,126,
    'ar=\n'
    'en=Allowed electrical design upload extensions\n',
    'ar=\n'
    'en=\n',
    '0=jpg\n'
    '1=jpeg\n'
    '2=png\n'
    '3=rar\n'
    '4=rar\n'
    '5=zip\n',
  3,6,0,1,UNIX_TIMESTAMP(),0);
  