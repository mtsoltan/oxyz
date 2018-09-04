INSERT IGNORE INTO products VALUES(6,0,
    'ar=\n'
    'en=Electric Design\n',
    'ar=\n'
    'en=Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin venenatis elit consequat mi feugiat ultricies. Maecenas pellentesque condimentum congue. Etiam dolor sem, auctor ut iaculis eu, finibus et lacus. Morbi finibus mi sed est mattis, eget vulputate nisi feugiat. Vestibulum in mi nibh. Curabitur dignissim tempor quam finibus fermentum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent ac ornare sapien. Etiam dapibus, neque ac condimentum interdum, diam arcu vestibulum ex, vitae convallis ex ex venenatis lectus. Cras convallis ultricies ligula sit amet consectetur. Morbi mattis neque at erat tempus efficitur. Donec semper tortor vel purus fringilla iaculis.',
  '/static/images/services/6.jpg',1,0,1,'',UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,1,
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
  