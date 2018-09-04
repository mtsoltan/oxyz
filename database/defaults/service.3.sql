INSERT IGNORE INTO products VALUES(3,1,
    'ar= تصنيع الدوائر الالكترونية\n'
    'en=PCB Milling\n',
    'ar=\n'
    'en=Quisque dignissim sem quis leo porttitor, ac convallis lorem vestibulum. Praesent consequat lorem pretium rhoncus tempus. Sed eu pulvinar lectus, vel viverra ipsum. Duis ac massa turpis. Praesent iaculis lacus in malesuada congue. Vivamus luctus sed quam vel ullamcorper. Morbi ultrices sodales ullamcorper. Nulla lacus nulla, semper id tortor at, volutpat sollicitudin enim. Maecenas nec facilisis orci. Nam vel convallis nulla. Donec pharetra fringilla mauris, sit amet consequat nunc mollis id. Cras nec lectus ac mauris imperdiet bibendum quis lacinia urna. Proin vehicula accumsan lectus quis fermentum.',
  '/static/images/services/3.jpg',1,0,1,'',UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,1,
    'ar=الملف المراد تنفيذه\n'
    'en=File to Carve\n',
    'ar=\n'
    'en=Please select a proteus / eagle / altium project or pcb layout file in one of the following extensions (pdsprj, pcbdoc, brd, pdf, gerber) or compress them (rar, zip) in case of multiple files. The maximum allowed upload size is 25 MB.\n',
    '__form::\n' 'name=file\n' 'type=file\n' 'id=file\n' 'required=1\n',
  3,3,1,0,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,127,
    'ar=ملحوظات\n'
    'en=Notes\n',
    'ar=برجاء اضافة اي ملحوظات الخذها في الاعتبار اثناء التنفيذ، و خصوصا الملحوظات المتعلقة بامتدادات الملفات\n'
    'en=Please add any notes regarding this job here, especially if you have any comments on file types / extensions.\n',
    '__form::\n' 'name=notes\n' 'type=textarea\n' 'id=notes\n' 'maxlength=2047\n',
  3,3,1,0,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,126,
    'ar=\n'
    'en=Allowed PCB CNC upload extensions\n',
    'ar=\n'
    'en=\n',
    '0=gerber\n'
    '1=pdsprj\n'
    '2=prjpcb\n'
    '3=pcbdoc\n'
    '4=brd\n'
    '5=pdf\n'
    '6=rar\n'
    '7=zip\n',
  3,3,0,1,UNIX_TIMESTAMP(),0);
