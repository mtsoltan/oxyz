INSERT IGNORE INTO products VALUES(1,1,
    'ar=الطباعة ثالثية الابعاد\n'
    'en=3D Printing\n',
    'ar='
    'تتم الطباعة باستخدام مواد مختلفة كالبلاستيك وانواع '
    'كتير من البوليمير ومن اسمها انت بيبقى عندك تصميم او '
    'حتى تصور بتطلب مننا انه نصممه واحنا بناخد التصميم '
    'ده بامتداد معين ونحوله لجزء ثلاثي الابعاد تقدر تستخدمه '
    'في وظيفته المطلوبة وده يشمل أي جزء سواء ميكانيكي '
    'مثل نموذج علبة تروس او كرسي محور، او غير ميكانيكي '
    'مثل ميداليات، هدايا، فازة، تمثال، تحف فنية، الخ '
    '\n'
    'en='
    'Provide us with a 3D model of a mechanical object like brackets, '
    'gearboxes, etc., or a non-mechanical object likemedals, gifts, '
    'trinkets, vases, statues, art models, etc., and we will 3D '
    'print it for you in plastic or other polymers as requested. '
    'If you have an imagination, but no 3D model, you can request '
    'our services and we will design it for you. '
    '\n',
  '/static/images/services/1.jpg',1,0,1,'',UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,1,
    'ar=الملف المطلوب طباعته\n'
    'en=File to Print\n',
    'ar='
    'برجاء رفع الملف المراد طباعته باحدي الامتدادات التالية'
    '(stl, obj)'
    'او ضغطهم في ملف'
    '(rar, zip)'
    'في حالة كانوا اكثر من ملف، بمساحة لا تتعدي 25 ميجا بايت\n'
    'en=Please select the file you wish to print in one of the following formats (stl, obj) or compress them (rar, zip) in case of multiple files. The maximum allowed upload size is 25 MB.\n',
    '__form::\n' 'name=file\n' 'type=file\n' 'id=file\n' 'required=1\n',
  3,1,1,0,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,2,
    'ar=لون الطباعة المطلوب\n'
    'en=Filament Color\n',
    'ar=برجاء اختيار اللون المراد استخدامه في الطباعة من الاختيارات المتاحة حاليا، وان كنت تريد لونا معينا غير موجود بالختيارات فبرجاء التنويه عن ذلك في خانة الملحوظات.\n'
    'en=Please select the color of the filament you want us to use while printing from the given options. If you wish to print in a color not provided in the options, please specify so in the notes section below.\n',
    '0=000000\n'
    '1=ffffff\n',
  3,1,1,1,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,3,
    'ar=\n'
    'en=Filament Material\n',
    'ar=\n'
    'en=Please select the material of the filament you want us to use while printing.\n',
    '0=ABS\n'
    '1=PLA\n',
  3,1,0,1,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,4,
    'ar=جودة الطباعة المطلوبة\n'
    'en=Printing Quality\n',
    'ar=برجاء اختيار جودة المنتج النهائي.\n'
    'en=Please specify the desired final product quality quality.\n',
    '[ar]\n'
    '0=200 مايكرون )جودة جيدة جدا(\n'
    '1=100 مايكرون )جودة ممتازة(\n'
    '2=100 مايكرون + تنعيم )جودة فائقة(\n'
    '[en]\n'
    '0=200μm (Very Good Quality)\n'
    '1=100μm (Excellent Quality)\n'
    '2=100μm + Refining (Superb Quality)\n',
  3,1,1,1,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,127,
    'ar=ملحوظات\n'
    'en=Notes\n',
    'ar=برجاء كتابة اي ملحوظة او تنويه خاص بالمطبوعات لالسترشاد بها ، وتحديدا ان كان مطلوب خامة غير ال PLA لاستخدامها في الطباعة\n'
    'en=Please add any notes regarding this printing job here, especially so if you wish to print this file using a material other than PLA.\n',
    '__form::\n' 'name=notes\n' 'type=textarea\n' 'id=notes\n' 'maxlength=2047\n',
  3,1,1,0,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,126,
    'ar=\n'
    'en=Allowed 3D printing upload extensions\n',
    'ar=\n'
    'en=\n',
    '0=stl\n'
    '1=obj\n'
    '2=rar\n'
    '3=zip\n',
  3,1,0,1,UNIX_TIMESTAMP(),0);
