import { Controller } from 'stimulus';
const $ = require('jquery');

export default class extends Controller {
    connect() {
        console.log('this is bm');
    }
}
