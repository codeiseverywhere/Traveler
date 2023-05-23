// This Foundation loader file was built from the following source file:
// - foundation-sites/dist/js/npm.js

// The 'zf' path alias is configured in the following config file:
// - build/webpack.base.conf.js

// Foundation core and utils - Best to import all of these
import {Foundation} from 'foundation-sites/dist/js/plugins/foundation.core';
import {Box} from 'foundation-sites/dist/js/plugins/foundation.util.box';
import {onImagesLoaded} from 'foundation-sites/dist/js/plugins/foundation.util.imageLoader';
import {Keyboard} from 'foundation-sites/dist/js/plugins/foundation.util.keyboard';
import {MediaQuery} from 'foundation-sites/dist/js/plugins/foundation.util.mediaQuery';
import {Motion, Move} from 'foundation-sites/dist/js/plugins/foundation.util.motion';
import {Nest} from 'foundation-sites/dist/js/plugins/foundation.util.nest';
import {Timer} from 'foundation-sites/dist/js/plugins/foundation.util.timer';
import {Touch} from 'foundation-sites/dist/js/plugins/foundation.util.touch';
import {Triggers} from 'foundation-sites/dist/js/plugins/foundation.util.triggers';
// Foundation plugins - Pick and choose your plugins here!
// If you comment out a plugin you will need to comment out
// the corresponding Foundation.plugin line also.
// This is a template project so they have all been imported.
//import { Abide } from 'foundation-sites/dist/js/plugins/foundation.abide';
import {Accordion} from 'foundation-sites/dist/js/plugins/foundation.accordion';
import {AccordionMenu} from 'foundation-sites/dist/js/plugins/foundation.accordionMenu';
//import { Drilldown } from 'foundation-sites/dist/js/plugins/foundation.drilldown';
import {Dropdown} from 'foundation-sites/dist/js/plugins/foundation.dropdown';
import {DropdownMenu} from 'foundation-sites/dist/js/plugins/foundation.dropdownMenu';
// import { Equalizer } from 'foundation-sites/dist/js/plugins/foundation.equalizer';
// import { Interchange } from 'foundation-sites/dist/js/plugins/foundation.interchange';
// import { Magellan } from 'foundation-sites/dist/js/plugins/foundation.magellan';
// import { OffCanvas } from 'foundation-sites/dist/js/plugins/foundation.offcanvas';
// import {Orbit} from 'foundation-sites/dist/js/plugins/foundation.orbit';
import {ResponsiveMenu} from 'foundation-sites/dist/js/plugins/foundation.responsiveMenu';
import {ResponsiveToggle} from 'foundation-sites/dist/js/plugins/foundation.responsiveToggle';
import {Reveal} from 'foundation-sites/dist/js/plugins/foundation.reveal';
// import { Slider } from 'foundation-sites/dist/js/plugins/foundation.slider';
// import { SmoothScroll } from 'foundation-sites/dist/js/plugins/foundation.smoothScroll';
// import { Sticky } from 'foundation-sites/dist/js/plugins/foundation.sticky';
import {Tabs} from 'foundation-sites/dist/js/plugins/foundation.tabs';
import {Toggler} from 'foundation-sites/dist/js/plugins/foundation.toggler';

// import { Tooltip } from 'foundation-sites/dist/js/plugins/foundation.tooltip';
// import { ResponsiveAccordionTabs } from 'foundation-sites/dist/js/plugins/foundation.responsiveAccordionTabs';

// Require non-modular scripts
require('motion-ui');
require('what-input');

Foundation.addToJquery(jQuery);

// Add Foundation Utils to Foundation global namespace for backwards
// compatibility.
Foundation.Box = Box;
Foundation.onImagesLoaded = onImagesLoaded;
Foundation.Keyboard = Keyboard;
Foundation.MediaQuery = MediaQuery;
Foundation.Motion = Motion;
Foundation.Move = Move;
Foundation.Nest = Nest;
Foundation.Timer = Timer;

// Touch and Triggers previously were almost purely sede effect driven,
// so nzf// need to add it to Foundation, just init them.
Touch.init(jQuery);
Triggers.init(jQuery, Foundation);
//Foundation.plugin(Abide, 'Abide');
//Foundation.plugin(Accordion, 'Accordion');
//Foundation.plugin(AccordionMenu, 'AccordionMenu');
//Foundation.plugin(Drilldown, 'Drilldown');
Foundation.plugin(Dropdown, 'Dropdown');
Foundation.plugin(DropdownMenu, 'DropdownMenu');
//Foundation.plugin(Equalizer, 'Equalizer');
//Foundation.plugin(Interchange, 'Interchange');
//Foundation.plugin(Magellan, 'Magellan');
//Foundation.plugin(OffCanvas, 'OffCanvas');
//Foundation.plugin(Orbit, 'Orbit');
Foundation.plugin(ResponsiveMenu, 'ResponsiveMenu');
Foundation.plugin(ResponsiveToggle, 'ResponsiveToggle');
Foundation.plugin(Reveal, 'Reveal');
//Foundation.plugin(Slider, 'Slider');
//Foundation.plugin(SmoothScroll, 'SmoothScroll');
//Foundation.plugin(Sticky, 'Sticky');
Foundation.plugin(Tabs, 'Tabs');
Foundation.plugin(Toggler, 'Toggler');
//Foundation.plugin(Tooltip, 'Tooltip');
//Foundation.plugin(ResponsiveAccordionTabs, 'ResponsiveAccordionTabs');

export default Foundation;
