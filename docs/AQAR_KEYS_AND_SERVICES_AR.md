# سجل الخدمات والمفاتيح — Novixa Aqar

> **هذه النسخة لا تحتوي على قيم سرية.**
> كل سر في هذا المستند مكتوب كـ `<محفوظ>` مع الإشارة إلى مكانه الحقيقي.
> النسخة الكاملة بالقيم أُرسلت إلى بريد المالك وإلى Google Drive.
>
> **السبب:** قاعدة المستودع في `CLAUDE.md` تمنع إيداع الأسرار في Git.
> تاريخ Git دائم ويُنسخ مع كل استنساخ للمستودع وكل تشغيل للـ CI، فإيداع
> مفتاح فيه يعني أنه لا يمكن سحبه لاحقًا حتى لو حُذف من الملف.
>
> **آخر تحديث:** 2026-09-11
>
> **تحديث 2026-10-11:** هذا السجل يصف بيئة Railway. بيئة العرض الحالية هي
> https://aqar-demo.novixa.dev (و https://aqar-demo2.novixa.dev لفرع `ui-improves`)
> — راجع `deploy/aqar-demo/README.md`. الحسابات التجريبية أصبحت على النطاق
> `@novixa.dev` (المصدر الوحيد: `app/Support/DemoAccounts.php`)، وأي بيئة
> تُبذر بياناتها بعد هذا التاريخ تستخدم العناوين الجديدة.

---

## 1. أين يعيش كل سر فعليًا

| المكان | ماذا يحتوي | كيف تصل إليه |
|---|---|---|
| **متغيرات Railway** | كل الأسرار التشغيلية الحيّة | لوحة Railway ← الخدمة `web` ← Variables |
| **بريد المالك** | نسخة كاملة بالقيم | رسالة بعنوان «سجل مفاتيح وخدمات Novixa Aqar» |
| **Google Drive** | نفس النسخة الكاملة | ملف `AQAR_KEYS_AND_SERVICES_FULL.md` |
| **هذا الملف** | الخريطة الكاملة بلا قيم | المستودع |

> **قاعدة:** عند تدوير أي مفتاح، حدّث Railway أولًا، ثم أعد إرسال النسخة
> الكاملة. لا تُدخل القيمة الجديدة في هذا الملف.

---

## 2. الاستضافة — Railway

| البند | القيمة |
|---|---|
| المشروع | `novixa-aqar-staging` |
| معرّف المشروع | `62a3b385-5e48-496b-9cb3-f16bee7086a8` |
| البيئة | `production` — `e5bbe8f5-3ead-4ef0-b4ea-43d5ad396e6d` |
| خدمة التطبيق | `web` — `1ff28b19-93b5-4dbe-845e-85c7103608bc` |
| خدمة قاعدة البيانات | `mysql` — `704601bc-e9fc-463e-bfd7-1fbbaac8d7fe` |
| النطاق العام | https://web-production-68b34.up.railway.app |
| المنطقة | `ams` (أمستردام) |
| لوحة التحكم | https://railway.com/project/62a3b385-5e48-496b-9cb3-f16bee7086a8 |

### إعدادات تشغيلية مهمة

| المتغير | القيمة | لماذا تهمّ |
|---|---|---|
| `PORT` | `8000` | فحص Railway الصحي يستخدم هذا المتغير. بدونه يفشل النشر بعد 300 ثانية. |
| `healthcheckPath` | `/up` | نقطة الفحص الصحي المدمجة في Laravel. |
| عدد العمّال | `1` | حدّ الذاكرة 1GB. عاملان تسببا سابقًا في انهيار OOM متكرر. |
| القرص | volume 500MB على `/var/www/html/storage/app` | يتقلّص دوره بعد نقل الوسائط إلى التخزين الخارجي. |

---

## 3. التخزين الخارجي — Railway Storage Bucket

| البند | القيمة |
|---|---|
| اسم الـ Bucket | `aqar-media` |
| المعرّف | `8e9d3f7b-ce07-482c-9b92-831c15393ed8` |
| المنطقة | `ams` |
| النوع | متوافق مع S3، خاص، مشفَّر أثناء التخزين، مبني على Tigris |
| التكلفة | ‎$0.015 لكل GB شهريًا · عمليات API والـ egress مجانية |

### المتغيرات على Railway

تُضبط **كمراجع متغيرات (Variable References)** لا كقيم منسوخة، فلا يغادر
المفتاح Railway أبدًا ولا يمر عبر أي أداة وسيطة:

```
AWS_ACCESS_KEY_ID      = ${{aqar-media.ACCESS_KEY_ID}}
AWS_SECRET_ACCESS_KEY  = ${{aqar-media.SECRET_ACCESS_KEY}}
AWS_BUCKET             = ${{aqar-media.BUCKET}}
AWS_DEFAULT_REGION     = ${{aqar-media.REGION}}
AWS_ENDPOINT           = ${{aqar-media.ENDPOINT}}
MEDIA_DISK             = media
MEDIA_URL              = https://web-production-68b34.up.railway.app
```

`AWS_BUCKET` هو **المفتاح الحاكم**: إن كان فارغًا يعود التطبيق تلقائيًا
إلى القرص المحلي. هذه هي طريقة التراجع الفورية عند أي مشكلة.

### كيف تُقدَّم الصور

الـ Bucket خاص ولا يدعم الملفات العامة. المسار `/media/{path}` في التطبيق
يستقبل الطلب ويعيد **توجيهًا 302 قابلًا للتخزين المؤقت** إلى رابط موقَّع
قصير العمر. النتيجة: الرابط في HTML ثابت، وبايتات الصورة تذهب من الـ
bucket إلى المتصفح مباشرة بلا مرور بـ PHP.

- عمر التوقيع: 120 دقيقة
- عمر الكاش: 3600 ثانية (أقصر من التوقيع عمدًا)
- الكود: `app/Http/Controllers/MediaProxyController.php`

> **تنبيه مهم عند التعديل:** `MEDIA_URL` يجب أن يكون **النطاق وحده بلا مسار**،
> أي `https://…app` وليس `https://…app/media`. السبب أن محوّل S3 في Laravel
> يبني الرابط هكذا: `MEDIA_URL` + `/` + جذر القرص (`media`) + `/` + مسار الملف.
> فجذر القرص هو ما يُنتج مقطع `/media` في الرابط، وإضافته يدويًا تُنتج
> `/media/media/...` فتفشل كل الصور بـ 404. ولهذا أيضًا يجب أن يبقى جذر القرص
> مطابقًا لبادئة المسار في `routes/web.php`.

### ملاحظة صيانة

يوجد bucket زائد باسم `buffered-toolchest-mClp`
(`3c2142ad-9d21-44d3-bd17-969c2fcd558d`) نتج عن استدعاء إنشاء واحد.
إنه **فارغ ولا يستخدمه شيء**. حذفه يتطلب موافقة على تغيير مرحلي من لوحة
Railway، ولم يُحذف تلقائيًا لأن حذف الموارد إجراء لا رجعة فيه.

**للحذف:** لوحة Railway ← اضغط على الـ bucket ← Settings ← Delete Bucket
← ثم Deploy للتغيير المرحلي.

---

## 4. البريد — Resend

| البند | القيمة |
|---|---|
| المزوّد | Resend — https://resend.com |
| لوحة التحكم | https://resend.com/emails |
| اسم المفتاح | `novixa-aqar-staging` |
| معرّف المفتاح | `1430123e-711c-4c7b-b79e-6c2b1ae682ae` |
| صلاحية المفتاح | `sending_access` (إرسال فقط — لا يستطيع إدارة الحساب) |
| قيمة المفتاح | `<محفوظ>` — في Railway باسم `MAIL_PASSWORD` |
| الطبقة المجانية | 3000 رسالة شهريًا · 100 رسالة يوميًا |

### المتغيرات على Railway

```
MAIL_MAILER        = smtp
MAIL_HOST          = smtp.resend.com
MAIL_PORT          = 2587
MAIL_USERNAME      = resend
MAIL_PASSWORD      = <محفوظ — مفتاح Resend>
MAIL_ENCRYPTION    = tls
MAIL_FROM_ADDRESS  = onboarding@resend.dev
MAIL_FROM_NAME     = Novixa Aqar
CONTACT_INBOX_EMAIL= <بريد المالك>
QUEUE_CONNECTION   = sync
```

> **لماذا `QUEUE_CONNECTION=sync`؟** عشرة من أصل اثني عشر إشعارًا في
> `app/Notifications/` تنفّذ `ShouldQueue`، ولا يعمل أي عامل طوابير في
> وضع `http` (راجع `.docker/octane/RoadRunner/supervisord.roadrunner.conf`
> — فيه برنامج `octane` فقط). بأي قيمة أخرى كانت هذه الإشعارات ستُصفّ في
> الطابور ولا تُرسل أبدًا.

### حالة التحقق: تم — الإرسال يعمل من التطبيق نفسه

رسالة أُرسلت عبر **مُرسِل Laravel المضبوط** (وليس عبر واجهة Resend البرمجية)
وأكّد Resend حالتها `delivered`. هذا هو نفس المسار الذي يسلكه نموذج «اتصل بنا»
وكل الإشعارات الاثني عشر.

> **المنفذ 2587 وليس 587 — وهذا مهم.**
> المنفذ القياسي 587 **محجوب من حاويات Railway**. الأسوأ أن الحجب لا يرفض
> الاتصال بل يتركه معلّقًا حتى تنتهي مهلة المقبس، فالعَرَض هو طلب بطيء ثم
> استثناء غامض، لا رسالة خطأ واضحة.
>
> اكتُشف هذا بأمر `mail:doctor` الذي يرسل رسالة عبر مُرسِل التطبيق فعليًا.
> قبله كان «البريد يعمل» مستندًا إلى نجاح استدعاء واجهة Resend البرمجية —
> وهي حقيقة عن Resend لا عن التطبيق. ينشر Resend المنفذين 2587 و2465
> كبديلين لهذه الحالة بالضبط.

### النطاق المخصّص — يحتاج خطوة منك

أُنشئ النطاق `aqar.novixa.dev` (`54d8e1f7-24a2-47c1-8926-b19d26a7a9ee`)
في منطقة `eu-west-1` وحالته `not_started` بانتظار سجلات DNS.

**قبل التوثيق:** لا يستطيع Resend الإرسال إلا إلى **بريد مالك الحساب**.
هذا كافٍ للديمو (إشعارات نموذج التواصل تذهب للمالك أصلًا) لكنه يمنع
مراسلة أي عنوان آخر.

**السجلات المطلوب إضافتها لدى مزوّد DNS لنطاق `novixa.dev`:**

| النوع | الاسم | القيمة | أولوية |
|---|---|---|---|
| TXT | `resend._domainkey.aqar` | `p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDkXZGNVTwMsS4MThEKr+ebTMqH+qmAWLgs/CoYh1PRBh7cvB4hGP9P7f634sWQegq/eiNcgjp/7hoC2O5/FAdTkHC0TvHxj0fIu3Tt6YPD6BJSYWsFsfMyZU7r6n5xFGnxjVwfbHKDKORYmC53L8tdWssvyhXsYZz+7ZbcU/prBwIDAQAB` | — |
| MX | `send.aqar` | `feedback-smtp.eu-west-1.amazonses.com` | 10 |
| TXT | `send.aqar` | `v=spf1 include:amazonses.com ~all` | — |
| CNAME | `rsend.aqar` | `send.forge.rmta.net` | — |

**بعد الإضافة:**
1. افتح https://resend.com/domains واضغط **Verify**.
2. غيّر `MAIL_FROM_ADDRESS` على Railway إلى `noreply@aqar.novixa.dev`.

---

## 5. الذكاء الاصطناعي

| البند | القيمة |
|---|---|
| الحالة | **يعمل بلا مفتاح** — التوليد محلي |
| المتغير | `OPENAI_API_KEY` — غير مضبوط عمدًا |
| نقطة النهاية | `OPENAI_BASE_URI` = `https://api.openai.com/v1` |
| النموذج | `OPENAI_CHAT_MODEL` = `gpt-4o-mini` |
| المهلة | `OPENAI_TIMEOUT` = 20 ثانية |

الخدمتان `AIDescriptionService` و`AIAssistantService` تعملان في وضعين:

- **بلا مفتاح (الوضع الحالي):** الوصف يُركَّب من بيانات العقار نفسه
  بلغة الزائر، والمساعد يجيب عبر `ChatbotService` القائم على القواعد
  والذي يفهم العربية والإنجليزية. هذا **وضع مدعوم ومقصود**.
- **بمفتاح:** تتحوّلان إلى المزوّد تلقائيًا، وتعودان للوضع المحلي عند
  أي خطأ أو انقطاع بدل إسقاط الصفحة.

**لتفعيل مزوّد مدفوع لاحقًا:**
1. أنشئ مفتاحًا من https://platform.openai.com/api-keys
2. أضفه على Railway باسم `OPENAI_API_KEY`.
3. أي مزوّد متوافق مع واجهة OpenAI يعمل بتغيير `OPENAI_BASE_URI` فقط.

---

## 6. الحسابات التجريبية

كلمة المرور لكل الحسابات: `password`

| الدور | البريد | اللوحة |
|---|---|---|
| مدير عام | `admin@novixa.dev` | `/admin` |
| موظف | `staff@novixa.dev` | `/staff` |
| وكيل | `agent@novixa.dev` | `/agent` |
| مشترٍ | `buyer@novixa.dev` | `/buyer` |
| بائع | `seller@novixa.dev` | `/seller` |
| مالك عقار | `landlord@novixa.dev` | `/landlord` |
| مستأجر | `tenant@novixa.dev` | `/tenant` |
| مقاول | `contractor@novixa.dev` | `/contractor` |

> يوجد أيضًا حساب مدير حقيقي تُنشئه متغيرات `ADMIN_EMAIL` و
> `ADMIN_PASSWORD` على Railway، وكلمته **ليست** `password`. القيمة
> محفوظة في Railway وفي النسخة الكاملة.
>
> **تحذير:** هذه حسابات ديمو بكلمة مرور معروفة. لا تُستخدم أبدًا في بيئة
> تحمل بيانات حقيقية — الـ seeder يرفض العمل في `production` لهذا السبب.

---

## 7. خدمات معرَّفة بلا مفاتيح — وهذا مقصود

هذه كلها مصرَّح بها في `config/services.php` بقيم فارغة. الإعلان عنها
ليس شكليًا: قبله كان `config()` يعيد `null` بصمت وتبدأ الخدمة مضبوطة
خطأً ثم تفشل لاحقًا بعيدًا عن السبب.

| الخدمة | المتغيرات | الحالة |
|---|---|---|
| Stripe | `STRIPE_KEY` · `STRIPE_SECRET` · `STRIPE_WEBHOOK_SECRET` | فارغة — لا مدفوعات حقيقية في الديمو |
| DocuSign | `DOCUSIGN_*` | فارغة — التوقيع الداخلي يعمل بدونها |
| Reverb | `REVERB_*` | فارغة — البث الفوري معطّل |
| WalkScore | `WALKSCORE_API_KEY` | فارغة |
| Plaid | `PLAID_*` | فارغة — خدمة سوق بريطاني |
| QuickBooks · Xero · Sage | `QUICKBOOKS_*` · `XERO_*` · `SAGE_ONLINE_*` | فارغة — محاسبة بريطانية |
| Rightmove · Zoopla · OnTheMarket · Jupix | `*_API_KEY` | فارغة — بوابات عقارية بريطانية |
| Locrating · Postcodes · AllAgents | `LOCRATING_*` · `POSTCODES_*` · `ALLAGENTS_*` | فارغة — خدمات بريطانية |
| فحص الخلفية والائتمان والتأمين | `BACKGROUND_CHECK_*` · `CREDIT_REPORT_*` · `INSURANCE_*` | فارغة |

> اختبار `tests/Feature/ServiceConfigurationTest.php` يمسح `app/` بحثًا عن
> كل `config('services.*')` ويفشل إن وُجد مفتاح غير معلَن، فلا يمكن أن
> يتكرر هذا الصنف من الأخطاء بصمت.

---

## 8. خدمات لا تحتاج مفاتيح

| الخدمة | التفاصيل |
|---|---|
| الخرائط | Leaflet + OpenStreetMap — مجانية بلا مفتاح |
| الخطوط | Google Fonts عبر CDN |
| الفحص الصحي | `/up` — مدمجة في Laravel |

---

## 9. الثغرات المتبقية — تحتاج إجراءً منك

| # | البند | لماذا لا يمكن تنفيذه آليًا | الرابط |
|---|---|---|---|
| 1 | توثيق نطاق البريد | يتطلب إضافة سجلات DNS | https://resend.com/domains |
| 2 | مراقبة توفّر مستمرة | فحص Railway يعمل **وقت النشر فقط** ولا يكتشف تعطّلًا بعده | https://uptimerobot.com |
| 3 | تنبيهات امتلاء القرص | لا تتاح عبر الواجهة البرمجية | لوحة Railway ← Volume ← Alerts |
| 4 | انتظار CI قبل النشر | إعداد المصدر غير متاح عبر الواجهة البرمجية | Railway ← Settings ← Source ← Wait for CI |
| 5 | حذف الـ bucket الزائد | الحذف إجراء لا رجعة فيه | القسم 3 أعلاه |

> **البند 2 هو الأهم.** حادثة سابقة استمرت أكثر من 60 ساعة والتطبيق ميت
> بينما Railway يعرض حالة نشر «ناجح». الفحص الصحي يمنع تكرار نشر فاشل،
> لكنه لا يراقب التطبيق بعد نجاح النشر.
>
> **الإعداد المقترح:** UptimeRobot — فحص HTTP(S) كل 5 دقائق على
> `https://web-production-68b34.up.railway.app/up` مع تنبيه بالبريد.
> مجاني ويستغرق دقيقتين.

---

## 10. إجراءات التدوير والطوارئ

### تدوير مفتاح Resend
1. https://resend.com/api-keys ← أنشئ مفتاحًا جديدًا بصلاحية `sending_access`.
2. حدّث `MAIL_PASSWORD` على Railway.
3. تحقّق من الإرسال، ثم احذف المفتاح القديم.

### تدوير مفاتيح التخزين
مفاتيح الـ bucket تُدار من Railway نفسه. لأنها مضبوطة كمراجع متغيرات،
التدوير من لوحة الـ bucket ينعكس تلقائيًا على التطبيق بلا تعديل يدوي.

### التراجع الفوري عن التخزين الخارجي
احذف `AWS_BUCKET` من متغيرات Railway. يعود القرصان `media` و`documents`
إلى القرص المحلي تلقائيًا. لا شيء يُحذف من الـ bucket، والملفات القديمة
ما تزال على الـ volume لأن أمر الترحيل ينسخ ولا ينقل.

### إذا توقّف الموقع
1. لوحة Railway ← الخدمة `web` ← Deployments ← اقرأ **سجل** آخر نشر.
2. **لا تثق بحالة النشر «SUCCESS» وحدها** — هذا بالضبط ما أخفى الحادثة السابقة.
3. راجع مقياس الذاكرة: الوضع الطبيعي ‎~0.21GB من 1GB.
4. ارتفاع مستمر نحو 1GB مع إعادة تشغيل متكررة = انهيار ذاكرة.

---

## 11. الملفات المرجعية

| الملف | المحتوى |
|---|---|
| `docs/AQAR_EXTERNAL_SERVICES_PLAN_AR.md` | خطة الربط والقرارات المعمارية |
| `docs/AQAR_BROWSER_TEST_PLAN_AR.md` | خطة الفحص الكامل عبر المتصفح |
| `docs/TEST_USERS.md` | الحسابات التجريبية |
| `docs/AQAR_PROGRESS.md` | سجل المراحل |
| `docs/AQAR_RULES.md` | بوابة الإصدار |
| `.env.example` | كل متغير بيئة مع شرحه |
