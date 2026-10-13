import React from 'react';
import {AbsoluteFill, Audio, interpolate, staticFile} from 'remotion';
import {C} from '../brand';
import {Bg, Bug, Progress, clamp} from '../components/Kit';
import {Beat, DesktopDemo, End, Features, Flow, Hook, Pain, PhoneDemo, Reveal, RolesGrid, Statement, Timeline, total} from '../components/Scenes';

// Every claim below is checked against content/research/FEATURE_REALITY_CLASSIFICATION.md
// and docs/product/03-FEATURES.md. Footage = real screenshots of the seeded demo.

const Frame: React.FC<{beats: Beat[]; music?: string; bug?: boolean}> = ({beats, music = 'aqar-bed', bug = false}) => {
  const d = total(beats);
  return (
    <AbsoluteFill style={{background: C.navy950, direction: 'rtl'}}>
      <Bg />
      <Audio src={staticFile(`audio/${music}.mp3`)} volume={(f) => interpolate(f, [0, 10, d - 45, d], [0, 0.6, 0.6, 0], clamp)} />
      <Timeline beats={beats} />
      {bug ? <Bug /> : null}
      <Progress />
    </AbsoluteFill>
  );
};

/* ===== 01 · HERO — "كل مكتبك العقاري في منصة واحدة" (AR) ===== */
export const heroBeats: Beat[] = [
  [<Hook lines={['مكتبك العقاري', 'كم *برنامج* يستخدم؟']} />, 75],
  [
    <Pain
      title="فين شغلك اليوم؟"
      items={[
        ['sheet', 'العقارات في ملف إكسل'],
        ['chat', 'العملاء في الواتساب'],
        ['file', 'العقود على الورق'],
        ['phone', 'الصيانة بالتلفون'],
      ]}
    />,
    120,
  ],
  [<Reveal text="عقار: كل هذا في *منصة* *واحدة*" sub="نظام عقاري متكامل من نوفيكسا" />, 90],
  [
    <PhoneDemo shot="m-home" caption="موقع عقاري احترافي *باسم* *مكتبك*" stops={[[8, 0], [80, 0.12]]} />,
    95,
  ],
  [
    <PhoneDemo shot="m-list" caption="بحث وفلترة *متقدمة*" sub="المدينة · السعر · النوع · الغرف" stops={[[8, 450], [85, 2900]]} />,
    95,
  ],
  [
    <PhoneDemo shot="m-detail" caption="صفحة كاملة *لكل* *عقار*" sub="صور · سعر · خريطة · واتساب · طلب معاينة" stops={[[8, 0], [40, 1100], [80, 1900]]} taps={[[0.3, 0.55, 82]]} />,
    100,
  ],
  [<DesktopDemo shot="d-admin-dark" caption="ولوحة تحكم *لإدارة* *كل* *شيء*" dark url="aqar-demo.novixa.dev/admin" zoom={[[10, 1, 0.5, 0.3], [70, 1.35, 0.25, 0.35]]} />, 105],
  [<RolesGrid title="8 أدوار … *لكل* *دور* لوحته" sub="وصلاحيات تحددها أنت" />, 120],
  [
    <Features
      title="وتفاصيل *تفرق*"
      items={[
        ['globe', 'عربي / English'],
        ['layers', 'RTL / LTR'],
        ['moon', 'الوضع الليلي'],
        ['phone', 'جوال وكمبيوتر'],
        ['coins', 'ريال يمني · سعودي · دولار'],
        ['calculator', 'حاسبات تمويل'],
        ['chat', 'مساعد محادثة'],
        ['wrench', 'طلبات الصيانة'],
      ]}
    />,
    105,
  ],
  [<End />, 165],
];
export const Hero: React.FC = () => <Frame beats={heroBeats} />;

/* ===== 01-EN · HERO (English, for Gulf / LinkedIn B2B) ===== */
export const heroEnBeats: Beat[] = [
  [<Hook en lines={['How many tools does', 'your *real-estate* *office* use?']} />, 80],
  [
    <Pain
      en
      title="Where does your work live today?"
      items={[
        ['sheet', 'Listings in a spreadsheet'],
        ['chat', 'Clients in WhatsApp'],
        ['file', 'Contracts on paper'],
        ['phone', 'Repairs over the phone'],
      ]}
    />,
    120,
  ],
  [<Reveal en text="Aqar: all of it in *one* *platform*" sub="A complete real-estate system by Novixa" />, 90],
  [<PhoneDemo en shot="m-home-en" caption="Your own *branded* property website" stops={[[8, 0], [80, 0.12]]} />, 95],
  [<DesktopDemo en shot="d-list-en" caption="Advanced *search* & filters" stops={[[10, 0], [80, 900]]} />, 95],
  [<PhoneDemo en shot="m-detail-en" caption="A full page for *every* listing" sub="Photos · price · map · WhatsApp · viewing request" stops={[[8, 0], [40, 1100], [80, 1900]]} />, 100],
  [<DesktopDemo en shot="d-admin-dark-en" caption="One dashboard to *run* *it* *all*" dark url="aqar-demo.novixa.dev/admin" zoom={[[10, 1, 0.5, 0.3], [70, 1.35, 0.7, 0.35]]} />, 105],
  [<RolesGrid en title="8 roles — *each* with its own panel" sub="Permissions you control" />, 120],
  [
    <Features
      en
      title="Details that *matter*"
      items={[
        ['globe', 'Arabic / English'],
        ['layers', 'RTL / LTR'],
        ['moon', 'Dark mode'],
        ['phone', 'Mobile & desktop'],
        ['coins', 'YER · SAR · USD'],
        ['calculator', 'Finance calculators'],
        ['chat', 'Chat assistant'],
        ['wrench', 'Maintenance requests'],
      ]}
    />,
    105,
  ],
  [<End en />, 165],
];
export const HeroEn: React.FC = () => <Frame beats={heroEnBeats} />;

/* ===== 02 · ROLES — one platform, 8 dashboards ===== */
export const rolesBeats: Beat[] = [
  [<Hook lines={['نظام واحد', '*8* *لوحات* *تحكم*']} sub="كل شخص يشوف اللي يخصّه فقط" />, 80],
  [<DesktopDemo shot="d-admin" step="الإدارة" caption="المستخدمين والأدوار *والصلاحيات*" zoom={[[10, 1, 0.5, 0.3], [70, 1.3, 0.3, 0.3]]} />, 90],
  [<DesktopDemo shot="d-staff" step="الموظفين" caption="العقارات والطلبات *والعملاء*" stops={[[10, 0], [75, 0.5]]} />, 85],
  [<DesktopDemo shot="d-agent" step="الوكلاء" caption="العملاء المحتملون *والمعاينات*" stops={[[10, 0], [75, 0.6]]} />, 85],
  [<DesktopDemo shot="d-landlord-leases" step="الملّاك" caption="العقود والإيجارات *والمدفوعات*" zoom={[[10, 1, 0.5, 0.2], [70, 1.4, 0.6, 0.25]]} />, 85],
  [<DesktopDemo shot="d-tenant" step="المستأجرين" caption="عقدي ودفعاتي *وطلبات* *الصيانة*" stops={[[10, 0], [75, 1]]} />, 85],
  [<DesktopDemo shot="d-buyer" step="المشترين" caption="المفضلة *وحجوزات* *المعاينة*" zoom={[[10, 1, 0.5, 0.2], [70, 1.35, 0.6, 0.2]]} />, 80],
  [<DesktopDemo shot="d-seller" step="البائعين" caption="عقاراتي المعروضة *والحجوزات*" zoom={[[10, 1, 0.5, 0.2], [70, 1.35, 0.6, 0.2]]} />, 80],
  [<DesktopDemo shot="d-contractor-orders" step="المقاولين" caption="مهام الصيانة *وتحديث* *حالتها*" zoom={[[10, 1, 0.5, 0.2], [70, 1.35, 0.6, 0.25]]} />, 85],
  [<Statement icon="shield" text="كل دور *بصلاحياته* … وأنت المتحكم" sub="الأدوار والصلاحيات قابلة للتعديل من لوحة الإدارة" />, 90],
  [<End />, 165],
];
export const Roles: React.FC = () => <Frame beats={rolesBeats} />;

/* ===== 03 · CLIENT JOURNEY — how your clients find your properties ===== */
export const searchBeats: Beat[] = [
  [<Hook lines={['عميلك يدوّر على بيت', 'من *جواله* …']} sub="هل يلقى عقاراتك؟" />, 80],
  [<PhoneDemo step="1" shot="m-home" caption="يفتح موقع *مكتبك*" stops={[[8, 0], [70, 0]]} taps={[[0.5, 0.42, 50]]} />, 80],
  [<PhoneDemo step="2" shot="m-list" caption="يفلتر *بسهولة*" sub="نوع العقار · بيع أو إيجار · السعر · الغرف" stops={[[8, 400], [60, 900]]} taps={[[0.5, 0.35, 30], [0.5, 0.55, 55]]} />, 90],
  [<PhoneDemo step="3" shot="m-list" caption="يتصفح *النتائج*" stops={[[5, 1600], [85, 5200]]} />, 90],
  [<PhoneDemo step="4" shot="m-detail" caption="يشوف كل *التفاصيل*" sub="صور · سعر · مساحة · موقع" stops={[[8, 0], [80, 1300]]} />, 90],
  [<PhoneDemo step="5" shot="m-detail" caption="يتواصل *بضغطة*" sub="واتساب · اتصال · حجز معاينة" stops={[[0, 1750], [60, 1750]]} taps={[[0.72, 0.17, 30], [0.28, 0.42, 60]]} />, 85],
  [<PhoneDemo step="6" shot="m-book" caption="ويحجز *موعد* *معاينة*" stops={[[8, 0], [80, 0.5]]} taps={[[0.2, 0.62, 78]]} />, 90],
  [<Statement icon="calendar" text="والطلب يوصل *لفريقك* مباشرة" sub="في لوحة التحكم — بدون ضياع رسائل" />, 85],
  [<End headline="خلّي عملاءك يلقونك *أسرع*" />, 165],
];
export const ClientSearch: React.FC = () => <Frame beats={searchBeats} />;

/* ===== 04 · MAINTENANCE — from request to done ===== */
export const maintBeats: Beat[] = [
  [<Hook lines={['حنفية خربانة …', 'كم *مكالمة* تحتاج؟']} />, 75],
  [
    <Flow
      title="الصيانة في عقار"
      steps={[
        ['home', 'المستأجر', 'يرفع الطلب من لوحته'],
        ['briefcase', 'المكتب', 'يراجع ويُسند المهمة'],
        ['hardhat', 'المقاول', 'ينفّذ ويحدّث الحالة'],
        ['check', 'الجميع', 'يتابعون حتى الإنجاز'],
      ]}
    />,
    150,
  ],
  [<DesktopDemo step="المستأجر" shot="d-tenant-maint-new" caption="طلب صيانة *في* *دقيقة*" zoom={[[10, 1, 0.5, 0.2], [70, 1.3, 0.6, 0.3]]} />, 95],
  [<DesktopDemo step="المستأجر" shot="d-tenant-maint" caption="ويتابع *حالة* *طلبه*" zoom={[[10, 1, 0.5, 0.2], [70, 1.45, 0.6, 0.3]]} />, 90],
  [<DesktopDemo step="المقاول" shot="d-contractor-orders" caption="المقاول يشوف *مهامه* فقط" zoom={[[10, 1, 0.5, 0.2], [70, 1.45, 0.6, 0.3]]} />, 95],
  [<Statement icon="wrench" text="لا مكالمات ضايعة … *كل* *شيء* *موثّق*" />, 80],
  [<End />, 165],
];
export const Maintenance: React.FC = () => <Frame beats={maintBeats} />;

/* ===== 05 · WHITE-LABEL OFFER — your brand, license not subscription ===== */
export const offerBeats: Beat[] = [
  [<Hook lines={['نظام عقاري كامل', '*باسم* *مكتبك*']} sub="مش قالب جاهز — نجهّزه ونخصّصه لك" />, 80],
  [
    <Features
      title="نخصّصه حسب *شغلك*"
      items={[
        ['palette', 'الشعار والألوان والهوية'],
        ['globe', 'الدومين والاستضافة'],
        ['users', 'المستخدمين والصلاحيات'],
        ['layers', 'وظائف حسب احتياجك'],
        ['building', 'بياناتك وفروعك'],
      ]}
    />,
    110,
  ],
  [<Statement icon="key" text="ترخيص *تملكه* — بدون اشتراك شهري إلزامي" />, 85],
  [<Statement icon="tag" text={"يبدأ من *\u2066$500\u2069*"} sub="والسعر النهائي حسب حجم المكتب والتخصيصات والخدمات" />, 90],
  [<End headline="اطلب عرضًا *مباشرًا* للنظام" sub="نشوف احتياجك ونجهّزه لنشاطك" />, 165],
];
export const Offer: React.FC = () => <Frame beats={offerBeats} />;

/* ===== 06 · INSIGHT — is this property a good investment? ===== */
export const insightBeats: Beat[] = [
  [<Hook lines={['قبل ما تشتري عقار …', 'هل هو *استثمار* *جيد؟*']} />, 80],
  [<PhoneDemo shot="m-detail" caption="كل عقار في عقار" sub="معه تحليل استثماري تقديري" stops={[[8, 0], [70, 1200]]} />, 85],
  [<PhoneDemo shot="m-detail" caption="العائد *المتوقع*" sub="تقدير العائد خلال 5 سنوات + مؤشر المخاطرة" stops={[[5, 4180], [80, 4300]]} />, 95],
  [<PhoneDemo shot="m-detail" caption="التدفق *النقدي*" sub="الإيجار المقدّر · المصاريف · صافي التدفق" stops={[[5, 4560], [80, 4640]]} />, 90],
  [<PhoneDemo shot="m-detail" caption="وموقعه *في* *السوق*" sub="مقارنة بمتوسط أسعار المنطقة" stops={[[5, 4790], [80, 4850]]} />, 85],
  [<Statement icon="chart" text="مش بس *صور* *وسعر* — قرار مبني على أرقام" sub="الأرقام تقديرية لأغراض العرض والتوضيح" />, 90],
  [<End />, 165],
];
export const Insight: React.FC = () => <Frame beats={insightBeats} />;

/* ===== 07 · MARKETERS — earn 30% commission ===== */
export const marketersBeats: Beat[] = [
  [<Hook lines={['تعرف مكتب عقاري؟', 'اربح *30%*']} sub="فرصة للموظفين والمسوّقين" />, 85],
  [
    <Flow
      title="كيف تشتغل؟"
      steps={[
        ['megaphone', 'عرّف', 'مكتبًا أو شركة عقارية على عقار'],
        ['handshake', 'نتواصل', 'نعرض النظام ونجهّزه للعميل'],
        ['coins', 'تربح', 'عمولة 30% من كل عميل يتم عن طريقك'],
      ]}
    />,
    140,
  ],
  [<End headline="انضم لفريق *التسويق*" sub="تواصل معنا على واتساب" price={false} />, 150],
];
export const Marketers: React.FC = () => <Frame beats={marketersBeats} />;

/* ===== 08 · EXPLAINER (16:9, ~85 s) for YouTube / Facebook / sales WhatsApp ===== */
export const explainerBeats: Beat[] = [
  [<Hook lines={['نوفيكسا عقار', 'نظام *متكامل* لمكاتب وشركات العقار']} sub="وليس مجرد موقع لعرض العقارات" />, 110],
  [
    <Pain
      title="المكتب العقاري اليوم"
      items={[
        ['sheet', 'العقارات في ملفات متفرقة'],
        ['chat', 'العملاء في الواتساب'],
        ['file', 'العقود على الورق'],
        ['phone', 'الصيانة بالتلفون'],
      ]}
    />,
    130,
  ],
  [<Reveal text="منصة *واحدة* تجمع كل شيء" sub="واجهة للعملاء + إدارة العقارات والعمليات + لوحات تحكم" />, 100],
  [<DesktopDemo step="1" shot="d-home" caption="واجهة عقارية *احترافية*" sub="باسم مكتبك وهويته" stops={[[10, 0], [110, 0.35]]} />, 125],
  [<DesktopDemo step="2" shot="d-list" caption="بحث وفلترة *متقدمة*" sub="الموقع · السعر · المساحة · النوع" stops={[[10, 0], [110, 0.45]]} />, 125],
  [<DesktopDemo step="3" shot="d-detail" caption="تفاصيل كل عقار" sub="معرض صور · خريطة · تحليل استثماري · طلب معاينة" stops={[[10, 0], [60, 0], [120, 1750]]} />, 135],
  [<DesktopDemo step="4" shot="d-admin-dark" dark caption="لوحة *الإدارة*" sub="العقارات · الحجوزات · الإيرادات · الموظفين" url="aqar-demo.novixa.dev/admin" zoom={[[10, 1, 0.5, 0.3], [90, 1.3, 0.25, 0.35]]} />, 120],
  [<DesktopDemo step="5" shot="d-admin-props" caption="إدارة العقارات *والوحدات*" sub="إضافة · تعديل · صور ومخططات · حالات الإعلان" zoom={[[10, 1, 0.5, 0.3], [90, 1.25, 0.6, 0.35]]} />, 120],
  [<RolesGrid title="8 أدوار بصلاحيات *مختلفة*" sub="الإدارة · الموظفين · الوكلاء · الملّاك · المستأجرين · المشترين · البائعين · المقاولين" />, 130],
  [
    <Flow
      title="العمليات العقارية *متصلة*"
      steps={[
        ['calendar', 'المعاينات', 'طلب من العميل ← فريقك'],
        ['file', 'العقود', 'الإيجارات والمدفوعات'],
        ['wrench', 'الصيانة', 'المستأجر ← المكتب ← المقاول'],
      ]}
    />,
    140,
  ],
  [<DesktopDemo step="6" shot="d-calc" caption="حاسبات *تمويلية* وعقارية" stops={[[10, 0], [110, 0.25]]} />, 110],
  [
    <Features
      title="جاهز *لسوقك*"
      items={[
        ['globe', 'عربي / English'],
        ['layers', 'RTL / LTR'],
        ['moon', 'الوضع الليلي'],
        ['phone', 'جوال · تابلت · كمبيوتر'],
        ['coins', 'ريال يمني · سعودي · دولار'],
        ['chat', 'مساعد محادثة ونماذج تواصل'],
      ]}
    />,
    120,
  ],
  [<Statement icon="palette" text="نخصّصه *باسم* *مكتبك*" sub="الهوية · الدومين · المستخدمين · وظائف إضافية حسب احتياجك" />, 110],
  [<Statement icon="key" text={"ترخيص يبدأ من *\u2066$500\u2069*"} sub="بدون اشتراك شهري إلزامي — السعر النهائي حسب حجم المشروع" />, 110],
  [<End headline="جرّب الديمو الآن … أو اطلب عرضًا *مباشرًا*" />, 180],
];
export const Explainer: React.FC = () => <Frame beats={explainerBeats} music="calm-bed" />;

/* ===== 09 · TRY IT — bilingual / dark / mobile proof ===== */
export const tryBeats: Beat[] = [
  [<Hook lines={['نفس النظام …', 'بأكثر من *شكل*']} />, 70],
  [<PhoneDemo shot="m-home" caption="عربي *RTL*" stops={[[0, 0]]} />, 60],
  [<PhoneDemo shot="m-home-en" en caption="English *LTR*" stops={[[0, 0]]} />, 60],
  [<PhoneDemo shot="m-home-dark" caption="الوضع *الليلي*" stops={[[0, 0], [55, 0.1]]} />, 60],
  [<PhoneDemo shot="m-chat" caption="مساعد *محادثة* للعملاء" stops={[[0, 0]]} />, 70],
  [<PhoneDemo shot="m-calc" caption="حاسبات *التمويل*" stops={[[0, 0], [55, 0.3]]} />, 65],
  [<PhoneDemo shot="m-admin" caption="ولوحة التحكم *من* *جوالك*" stops={[[0, 0], [60, 0.5]]} />, 70],
  [<End headline="جرّبه *بنفسك* الآن" />, 160],
];
export const TryIt: React.FC = () => <Frame beats={tryBeats} />;
