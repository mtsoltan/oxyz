-- entity_id = 1 on entity_type = 0 (site) signifies customer fields.
INSERT IGNORE INTO keystore VALUES(NULL,1,1,
    'ar=رقم الهاتف المحمول\n'
    'en=Mobile Phone Number\n',
    'ar=برجاء ادخال رقم الهاتف المحمول الخاص بسيادتكم للتواصل من خالله فيما بعد، وان كنت عميل سابق فبرجاء ادخال نفس رقم المحمول المسجل لدينا من قبل.\n'
    'en=Please enter the phone number that we will use to contact you. If you have placed an order before, and still own the phone number used in your previous order, please use that phone number.\n',
    '__form::\n' 'name=phone\n' 'type=tel\n' 'id=phone\n' 'maxlength=13\n' 'required=1\n',
  0,1,1,0,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,2,
    'ar=الاسم\n'
    'en=Name\n',
    'ar=برجاء ادخال الاسم الثلاثي.\n'
    'en=Please enter your full name.\n',
    '__form::\n' 'name=name\n' 'type=text\n' 'id=name\n' 'maxlength=255\n' 'required=1\n' 'pattern=.+ .+ .+\n',
  0,1,1,0,UNIX_TIMESTAMP(),0);
INSERT IGNORE INTO keystore VALUES(NULL,1,3,
    'ar=البريد الالكتروني )الايميل(\n'
    'en=Email\n',
    'ar=يمكنك ترك هذا الحقل فارغا ان لم تكن تمتلك بريد الكتروني\n'
    'en=You can leave this empty if you do not have an email.\n',
    '__form::\n' 'name=email\n' 'type=email\n' 'id=email\n' 'maxlength=255\n',
  0,1,1,0,UNIX_TIMESTAMP(),0);
-- Province Select
INSERT IGNORE INTO keystore VALUES(NULL,1,254,
    'ar=المحافظة\n'
    'en=Province\n',
    'ar=يمكنك ترك هذا الحقل فارغا ان لم تفضل الافصاح عن هذه المعلومات\n'
    'en=You can leave this empty if you do not wish to disclose this information.\n',
    '[ar]\n'
    '255=\n'
    '13=الأقصر\n'
    '0=الإسكندرية\n'
    '23=الشرقية\n'
    '2=أسيوط\n'
    '3=البحيرة\n'
    '5=القاهرة\n'
    '7=دمياط\n'
    '12=كفر الشيخ\n'
    '16=المنوفية\n'
    '15=المنيا\n'
    '19=بورسعيد\n'
    '20=القليوبية\n'
    '1=أسوان\n'
    '11=الإسماعيلية\n'
    '4=بني سويف\n'
    '6=الدقهلية\n'
    '8=الفيوم\n'
    '9=الغربية\n'
    '10=الجيزة\n'
    '22=البحر الأحمر\n'
    '21=قنا\n'
    '25=جنوب سيناء\n'
    '18=شمال سيناء\n'
    '26=السويس\n'
    '24=سوهاج\n'
    '17=الوادي الجديد\n'
    '14=مطروح\n'
    '[en]\n'
    '255=\n'
    '0=Alexandria\n'
    '1=Aswan\n'
    '2=Asyut\n'
    '3=Beheira\n'
    '4=Beni Suef\n'
    '5=Cairo\n'
    '6=Dakahlia\n'
    '7=Damietta\n'
    '8=Faiyum\n'
    '9=Gharbia\n'
    '10=Giza\n'
    '11=Ismailia\n'
    '12=Kafr El Sheikh\n'
    '13=Luxor\n'
    '14=Matruh\n'
    '15=Minya\n'
    '16=Monufia\n'
    '17=New Valley\n'
    '18=North Sinai\n'
    '19=Port Said\n'
    '20=Qalyubia\n'
    '21=Qena\n'
    '22=Red Sea\n'
    '23=Sharqia\n'
    '24=Sohag\n'
    '25=South Sinai\n'
    '26=Suez\n',
  0,1,1,0,UNIX_TIMESTAMP(),0);
