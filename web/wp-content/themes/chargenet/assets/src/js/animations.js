import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

// Sections add their own animations here; skip all motion for users who ask for less.
export const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
