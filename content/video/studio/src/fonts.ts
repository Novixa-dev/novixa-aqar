import '@fontsource/cairo/arabic-500.css';
import '@fontsource/cairo/arabic-700.css';
import '@fontsource/cairo/arabic-800.css';
import '@fontsource/cairo/arabic-900.css';
import '@fontsource/cairo/latin-500.css';
import '@fontsource/cairo/latin-700.css';
import '@fontsource/cairo/latin-800.css';
import '@fontsource/poppins/latin-500.css';
import '@fontsource/poppins/latin-600.css';
import '@fontsource/poppins/latin-700.css';
import '@fontsource/poppins/latin-800.css';
import {continueRender, delayRender} from 'remotion';

const handle = delayRender('fonts');
Promise.all([
  document.fonts.load('500 40px Cairo', 'عقار'),
  document.fonts.load('700 40px Cairo', 'عقار'),
  document.fonts.load('800 40px Cairo', 'عقار'),
  document.fonts.load('900 40px Cairo', 'عقار'),
  document.fonts.load('700 40px Cairo', 'Aqar 123'),
  document.fonts.load('800 40px Cairo', 'Aqar 123'),
  document.fonts.load('500 40px Poppins', 'Aqar'),
  document.fonts.load('600 40px Poppins', 'Aqar'),
  document.fonts.load('700 40px Poppins', 'Aqar'),
  document.fonts.load('800 40px Poppins', 'Aqar'),
]).then(() => continueRender(handle));
