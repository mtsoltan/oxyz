INSERT IGNORE INTO products VALUES(3,1,
    'ar= تصنيع الدوائر الالكترونية\n'
    'en=PCB Milling\n',
    'ar='
    'ان كنت تعاني من تحميض دوائرك الكهربية و دفع ارقام '
    'كبيرة في التحميض التقليدي، نحن ننحت ونثقب الدوائر '
    'الالكترونية بسرعة ودقة عالية بكفاءة اعلي بشكل ملحوظ '
    'من التحميض التقليدي. '
    '\n'
    'en='
    'Sick of etching your boards and paying for expensive photoresist '
    'masks and having to deal with the parasitic effects of over-exposure '
    'to acid? We mill and drill PCBs in high accuracy and resolution in '
    'much efficient manner compared to the regular etching method. '
    '\n',
  '/static/images/services/3.jpg',1,0,1,'',UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,1,
    'ar=الملف المراد تنفيذه\n'
    'en=File to Carve\n',
    'ar='
    'برجاء رفع ملف لمشروع الدائرة المراد تنفيذها علي برنامج'
    ' Protus او Eagle او Altium '
    'بالامتدادات التالية : '
    '(pdsprj, pcbdoc, brd, pdf, gerber)'
    ' او ضغطهم في ملف '
    'Zip او Rar'
    ' في حالة وجود اكثر من ملف. مسموح برف ملفات بمساحة قصوي لا تتعدي 25 ميجا بايت.'
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
