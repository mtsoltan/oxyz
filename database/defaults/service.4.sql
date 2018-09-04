INSERT IGNORE INTO products VALUES(4,0,
    'ar=النحت علي ماكينة سي ان سي 3 محاور\n'
    'en=3-Axis CNC\n',
    'ar=\n'
    'en=Aliquam quam purus, egestas a lobortis ac, fringilla eu nisi. Integer et turpis pulvinar, pulvinar metus ac, placerat enim. Vivamus nec tempor ipsum. Nunc tincidunt eleifend orci in dignissim. Aenean lobortis lectus a lacus maximus, vitae convallis felis semper. Fusce at est libero. Nunc ac eros ligula. Vestibulum euismod sapien quis pharetra mattis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Ut eu eros id ipsum maximus mollis. Fusce mattis tellus tellus. Maecenas euismod hendrerit lectus sed hendrerit. In finibus dui volutpat ex mattis ullamcorper.',
  '/static/images/services/4.jpg',1,0,1,'',UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,1,
    'ar=\n'
    'en=\n',
    '__form::\n' 'name=file\n' 'type=file\n' 'id=file\n' 'required=1\n',
  3,4,1,0,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,127,
    'ar=ملحوظات\n'
    'en=Notes\n',
    'ar=برجاء اضافة اي ملحوظات الخذها في الاعتبار اثناء التنفيذ، و خصوصا الملحوظات المتعلقة بامتدادات الملفات\n'
    'en=Please add any notes regarding this job here, especially if you have any comments on file types / extensions.\n',
    '__form::\n' 'name=notes\n' 'type=textarea\n' 'id=notes\n' 'maxlength=2047\n',
  3,4,1,0,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,126,
    'ar=\n'
    'en=Allowed 3-axis CNC upload extensions\n',
    'ar=\n'
    'en=\n',
    '0=stl\n'
    '1=obj\n'
    '2=rar\n'
    '3=zip\n',
  3,4,0,1,UNIX_TIMESTAMP(),0);
