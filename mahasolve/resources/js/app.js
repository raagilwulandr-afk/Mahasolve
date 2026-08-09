import Alpine from 'alpinejs';
import { injectSpeedInsights } from '@vercel/speed-insights';

window.Alpine = Alpine;
Alpine.start();

// Initialize Vercel Speed Insights
injectSpeedInsights();
