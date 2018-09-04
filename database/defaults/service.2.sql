INSERT IGNORE INTO products VALUES(2,1,
    'ar=النحت والتقطيع بالليزر\n'
    'en=Laser Carving and Cutting\n',
    'ar=\n'
    'en=Nunc metus erat, malesuada sit amet arcu a, accumsan interdum ex. Aenean sit amet dui iaculis, fringilla lacus ut, viverra augue. Aliquam diam massa, euismod ac urna ut, suscipit fermentum ex. In lorem ex, facilisis id quam ac, consectetur porttitor sapien. Nunc a erat ut augue cursus porta ac quis est. In commodo auctor justo, ac faucibus risus dignissim eu. Nunc magna sem, posuere ac lacus non, egestas consequat risus. Vivamus eget sollicitudin quam. Cras pharetra ligula eget turpis blandit pretium. Vestibulum maximus tristique purus in dapibus. Ut ultrices tellus nec nunc elementum lobortis.',
  '/static/images/services/2.jpg',1,0,1,'',UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,1,
    'ar=الملف المراد تنفيذه\n'
    'en=File to Carve\n',
    'ar=برجاء رفع الملف المراد تنفيذه باحدي الامتدادات التالية'
    '(dwg, dxf)'
    'او ضغطهم في ملف'
    '(rar, zip)'
    'في حالة كانوا اكثر من ملف، بمساحة لا تتعدي 25 ميجا بايت\n'
    'en=Please select the file you wish to carve in one of the following formats (stl, obj) or compress them (rar, zip) in case of multiple files. The maximum allowed upload size is 25 MB.\n',
    '__form::\n' 'name=file\n' 'type=file\n' 'id=file\n' 'required=1\n',
  3,2,1,1,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,2,
    'ar=هل تريد التقطيع ام الحفر؟\n'
    'en=Do you wish to cut or engrave?\n',
    'ar=برجاء اختيار هل تريد القطع او الحفر او كالهما. و برجاء ترك اي ملحوظات عن الملفات التي ستقطع والملفات التي ستنحت في صندوق الملحوظات بالاسفل، وسيتم مراجعة كافة البيانات هاتفيا او بزيارتكم لمكتبنا.\n'
    'en=Please select whether you wish to cut, engrave, or both. Please leave any notes about files that will be cut or engraved in the notes section below. This information needs to be gone through again on the phone or by visiting our office.\n',
    '[ar]\n'
    '0=قطع\n'
    '1=حفر\n'
    '2=كالهما\n'
    '[en]\n'
    '0=Cutting\n'
    '1=Engraving\n'
    '2=Both\n',
  3,2,1,1,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,3,
    'ar=الخامة\n'
    'en=Material\n',
    'ar=برجاء اختيار الخامة المطلوب استخدامها، واذا كانت الملفات ستنفذ بخامات مختلفة فبرجاء توضيع ذلك في صندوق الملحوظات بالاسفل، وسيتم مراجعة كافة البيانات علي المحمول او بزيارتكم لمكتبنا.\n'
    'en=Please select the material and thickness you want us to use. If multiple materials / thicknesses are to be used, please select "Other" and elaborate in the notes section below, and we will go over the information with you on the phone, or by visiting our office.\n',
    '[ar]\n'
    '0=MDF تخانة 3 ملي ، لونه فاتح\n'
    '1=MDF تخانة 6 ملي ، لونه غامق\n'
    '2=ابالكاش تخانة 3 ملي\n'
    '3=ابالكاش تخانة 6 ملي\n'
    '4=اكريلك تخانة 3 ملي\n'
    '5=اكريلك تخانة 6 ملي\n'
    '6=جلود او اقمشة\n'
    '7=رخام او زجاج )للنحت فقط(\n'
    '[en]\n'
    '0=3mm Light-colored MDF\n'
    '1=6mm Dark-colored MDF\n'
    '2=3mm Plywood\n'
    '3=6mm Plywood\n'
    '4=3mm Acrylic\n'
    '5=6mm Acrylic\n'
    '6=Leather or fabric\n'
    '7=Ceramics and Glass (Engraving Only)\n',
  3,2,1,1,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,127,
    'ar=ملحوظات\n'
    'en=Notes\n',
    'ar=برجاء اضافة اي ملحوظات تخص المنتج النهائي لمراعتها اثناء التصنيع\n'
    'en=Please add any notes about the job / final product here.\n',
    '__form::\n' 'name=notes\n' 'type=textarea\n' 'id=notes\n' 'maxlength=2047\n',
  3,2,1,1,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,126,
    'ar=\n'
    'en=Allowed laser cutting upload extensions\n',
    'ar=\n'
    'en=\n',
    '0=dwg\n'
    '1=dxf\n'
    '2=rar\n'
    '3=zip\n',
  3,2,0,1,UNIX_TIMESTAMP(),0);
