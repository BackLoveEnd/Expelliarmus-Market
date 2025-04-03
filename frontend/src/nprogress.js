import NProgress from 'nprogress';
import 'nprogress/nprogress.css';
import './assets/nprogress.css';

NProgress.configure({ showSpinner: false });

export const start = () => NProgress.start();
export const done = () => NProgress.done();