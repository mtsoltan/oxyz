INSERT IGNORE INTO products VALUES(5,1,
    'ar=تصميم ميكانيكي هندسي وخردوات\n'
    'en=Mechanical and Gadget Design\n',
    'ar=\n'
    'en=Pellentesque mattis gravida sem sed viverra. Suspendisse potenti. Fusce pellentesque tincidunt lorem, sed molestie ante ullamcorper eget. Donec elit nisi, dictum venenatis nisl at, finibus ultrices nisi. Ut pulvinar gravida tempus. Aliquam sit amet tortor a lorem feugiat blandit. Sed quis aliquet eros, consequat bibendum turpis. Donec sed ante sed orci faucibus sagittis. Mauris et pulvinar eros. Curabitur pharetra odio massa, ac laoreet purus hendrerit in. Phasellus vel bibendum quam. Fusce suscipit sollicitudin arcu, sit amet semper magna tempus sit amet. Maecenas aliquet, sem ac bibendum imperdiet, nunc metus consectetur mauris, vel facilisis justo diam faucibus velit. Praesent venenatis urna purus, non sodales sem efficitur quis. Sed malesuada rhoncus arcu sed porttitor.',
  '/static/images/services/5.jpg',1,0,1,'',UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,1,
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
