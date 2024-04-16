import{K as f,U as s3,a0 as d3,B as b,C as R,$ as N,G as p,a9 as $,a1 as W,W as I,a3 as m3,a as v3,an as g3,A as j,r as X,c as T,a5 as u3,aF as Y,ai as p3,aj as h3,H as f3,at as b3,ab as K,F as x,a4 as w3,az as y3,aA as x3}from"./iconify-4eda29ca.js";const F=typeof window<"u",H=()=>{};function z3(e,a=200,o={}){return function(s,r){return function(...l){s(()=>r.apply(this,l),{fn:r,thisArg:this,args:l})}}(function(s,r={}){let l,t;return m=>{const c=x(s),u=x(r.maxWait);if(l&&clearTimeout(l),c<=0||u!==void 0&&u<=0)return t&&(clearTimeout(t),t=null),m();u&&!t&&(t=setTimeout(()=>{l&&clearTimeout(l),t=null,m()},u)),l=setTimeout(()=>{t&&clearTimeout(t),t=null,m()},c)}}(a,o),e)}var _3=Object.defineProperty,S3=Object.defineProperties,C3=Object.getOwnPropertyDescriptors,J=Object.getOwnPropertySymbols,k3=Object.prototype.hasOwnProperty,A3=Object.prototype.propertyIsEnumerable,Z=(e,a,o)=>a in e?_3(e,a,{enumerable:!0,configurable:!0,writable:!0,value:o}):e[a]=o,M3=(e,a)=>{for(var o in a||(a={}))k3.call(a,o)&&Z(e,o,a[o]);if(J)for(var o of J(a))A3.call(a,o)&&Z(e,o,a[o]);return e};const G=F?window:void 0;function E(...e){let a,o,s,r;if(typeof e[0]=="string"?([o,s,r]=e,a=G):[a,o,s,r]=e,!a)return H;let l=H;const t=w3(()=>function(u){var k;const w=x(u);return(k=w==null?void 0:w.$el)!=null?k:w}(a),u=>{l(),u&&(u.addEventListener(o,s,r),l=()=>{u.removeEventListener(o,s,r),l=H})},{immediate:!0,flush:"post"}),m=()=>{t(),l()};var c;return c=m,y3()&&x3(c),m}function B3(e,a,o={}){const{target:s=G,eventName:r="keydown",passive:l=!1}=o,t=typeof(m=e)=="function"?m:typeof m=="string"?c=>c.key===m:Array.isArray(m)?c=>m.includes(c.key):m?()=>!0:()=>!1;var m;return E(s,r,c=>{t(c)&&a(c)},l)}const e3=typeof globalThis<"u"?globalThis:typeof window<"u"?window:typeof global<"u"?global:typeof self<"u"?self:{},a3="__vueuse_ssr_handlers__";e3[a3]=e3[a3]||{};var t3,n3,I3=Object.defineProperty,O3=Object.defineProperties,E3=Object.getOwnPropertyDescriptors,i3=Object.getOwnPropertySymbols,L3=Object.prototype.hasOwnProperty,j3=Object.prototype.propertyIsEnumerable,o3=(e,a,o)=>a in e?I3(e,a,{enumerable:!0,configurable:!0,writable:!0,value:o}):e[a]=o;function T3(e,a={}){var o,s;const r=(o=a.draggingElement)!=null?o:G,l=j((s=a.initialValue)!=null?s:{x:0,y:0}),t=j(),m=n=>!a.pointerTypes||a.pointerTypes.includes(n.pointerType),c=n=>{x(a.preventDefault)&&n.preventDefault(),x(a.stopPropagation)&&n.stopPropagation()},u=n=>{var d;m(n)&&t.value&&(l.value={x:n.pageX-t.value.x,y:n.pageY-t.value.y},(d=a.onMove)==null||d.call(a,l.value,n),c(n))},k=n=>{var d;m(n)&&t.value&&(t.value=void 0,(d=a.onEnd)==null||d.call(a,l.value,n),c(n))};return F&&(E(e,"pointerdown",n=>{var d;if(!m(n)||x(a.exact)&&n.target!==x(e))return;const v=x(e).getBoundingClientRect(),z={x:n.pageX-v.left,y:n.pageY-v.top};((d=a.onStart)==null?void 0:d.call(a,z,n))!==!1&&(t.value=z,c(n))},!0),E(r,"pointermove",u,!0),E(r,"pointerup",k,!0)),w=((n,d)=>{for(var v in d||(d={}))L3.call(d,v)&&o3(n,v,d[v]);if(i3)for(var v of i3(d))j3.call(d,v)&&o3(n,v,d[v]);return n})({},function(n){if(!f3(n))return Y(n);const d=Array.isArray(n.value)?new Array(n.value.length):{};for(const v in n.value)d[v]=b3(()=>({get:()=>n.value[v],set(z){if(Array.isArray(n.value)){const M=[...n.value];M[v]=z,n.value=M}else n.value=(B=M3({},n.value),S3(B,C3({[v]:z})));var B}}));return d}(l)),A={position:l,isDragging:T(()=>!!t.value),style:T(()=>`left:${l.value.x}px;top:${l.value.y}px;`)},O3(w,E3(A));var w,A}F&&(window!=null&&window.navigator)&&((t3=window==null?void 0:window.navigator)!=null&&t3.platform)&&/iP(ad|hone|od)/.test((n3=window==null?void 0:window.navigator)==null?void 0:n3.platform);const V3=["a","s","d","w","q","e","A","S","D","W","Q","E","ArrowUp","ArrowDown","ArrowLeft","ArrowRight","Escape"," "];var V=g3({name:"V3ImgPreview",props:{showToolbar:{type:Boolean,default:!0},showArrowBtn:{type:Boolean,default:!0},keyboard:{type:Boolean,default:!0},url:{type:String,default:void 0},escClose:{type:Boolean,default:!0},images:{type:Array,default:()=>[]},showCloseBtn:{type:Boolean,default:!0},index:{type:Number,default:0},on_unmount_v3_ima_preview_app:{type:Function}},setup(e,a){let{emit:o}=a;const s=j(null),r=j(null),{style:l}=T3(r),t=X({visible:!0,imgState:"loading",src:"",imgIndex:e.index}),m=T(()=>{var i;return((i=e.images)===null||i===void 0?void 0:i.length)>1&&e.showArrowBtn}),c=X({imgScale:1,imgRotate:0}),u=T(()=>{var i;return((i=e.images)===null||i===void 0?void 0:i.length)>1}),k=z3(i=>{if(!e.keyboard)return!1;i.preventDefault();const{key:g}=i;return["s","S","ArrowDown"].includes(g)?n(-.1,!1):["w","W","ArrowUp"].includes(g)?n(.1,!1):g===" "?w():g==="Escape"&&e.escClose?B():["E","e"].includes(g)?A(!0):["Q","q"].includes(g)?A(!1):["a","A","ArrowLeft"].includes(g)?M(!1):["d","D","ArrowRight"].includes(g)?M(!0):void 0},200);B3(V3,k);const w=()=>{c.imgScale=1,c.imgRotate=0,r.value.style.top="0",r.value.style.left="0"};function A(i){c.imgRotate+=90*(i?1:-1)}function n(i){let g=arguments.length>1&&arguments[1]!==void 0&&arguments[1];c.imgScale<=.2&&i<0||(g?c.imgScale=i:c.imgScale+=i)}function d(i){i.preventDefault(),n(i.deltaY<0?.05:-.05)}function v(i){t.imgState="loading",(g=>{const _=new Image;return _.src=g,new Promise((L,S)=>{_.onload=()=>{L(g)},_.onerror=()=>{S(g)}})})(i).then(()=>{t.imgState="success",t.src=i,w()}).catch(()=>{t.imgState="error"})}function z(){K(()=>{E(s.value,"mousewheel",d,!1),w(),K(()=>e.url!==void 0?v(e.url):Array.isArray(e.images)&&e.images.length>0?v(e.images[t.imgIndex]):console.error("images is not Array or Array length is 0"))})}function B(){var i;t.visible=!1,o("close"),(i=e.on_unmount_v3_ima_preview_app)===null||i===void 0||i.call(e)}function M(i){u.value&&(i?(t.imgIndex++,t.imgIndex>e.images.length-1&&(t.imgIndex=0)):(t.imgIndex--,t.imgIndex<0&&(t.imgIndex=e.images.length-1)),v(e.images[t.imgIndex]))}return u3(()=>{window.__V3__IMG__PREVIEW__LOAD__ICON__SVG__||function(i){i.__V3__IMG__PREVIEW__LOAD__ICON__SVG__=!0;var g,_,L,S,P,U='<svg><symbol id="v3-img-close" viewBox="0 0 1045 1024"><path d="M282.517333 213.376l-45.354666 45.162667L489.472 512 237.162667 765.461333l45.354666 45.162667L534.613333 557.354667l252.096 253.269333 45.354667-45.162667-252.288-253.44 252.288-253.482666-45.354667-45.162667L534.613333 466.624l-252.096-253.226667z"  ></path></symbol><symbol id="v3-img-img-error" viewBox="0 0 1024 1024"><path d="M704 328a72 72 0 1 0 144 0 72 72 0 1 0-144 0z"  ></path><path d="M999.904 116.608a32 32 0 0 0-21.952-10.912L521.76 73.792a31.552 31.552 0 0 0-27.2 11.904l-92.192 114.848a32 32 0 0 0 0.672 40.896l146.144 169.952-147.456 194.656 36.48-173.376a32 32 0 0 0-11.136-31.424L235.616 245.504l79.616-125.696a32 32 0 0 0-29.28-49.024L45.76 87.552a32 32 0 0 0-29.696 34.176l55.808 798.016a32.064 32.064 0 0 0 34.304 29.696l176.512-13.184c17.632-1.312 30.848-16.672 29.504-34.272s-16.576-31.04-34.304-29.536L133.44 883.232l-6.432-92.512 125.312-12.576a32 32 0 0 0 28.672-35.04 32.16 32.16 0 0 0-35.04-28.672L122.56 726.848 82.144 149.184l145.152-10.144-60.96 96.224a32 32 0 0 0 6.848 41.952l198.4 161.344-58.752 279.296a30.912 30.912 0 0 0 0.736 14.752 31.68 31.68 0 0 0 1.408 11.04l51.52 154.56a31.968 31.968 0 0 0 27.456 21.76l523.104 47.552a32.064 32.064 0 0 0 34.848-29.632l55.776-798.048a32.064 32.064 0 0 0-7.776-23.232z m-98.912 630.848l-412.576-39.648a31.52 31.52 0 0 0-34.912 28.768 32 32 0 0 0 28.8 34.912l414.24 39.808-6.272 89.536-469.728-42.72-39.584-118.72 234.816-310.016a31.936 31.936 0 0 0-1.248-40.192L468.896 219.84l65.088-81.056 407.584 28.48-40.576 580.192z"  ></path></symbol><symbol id="v3-img-loading" viewBox="0 0 1024 1024"><path d="M834.7648 736.3584a5.632 5.632 0 1 0 11.264 0 5.632 5.632 0 0 0-11.264 0z m-124.16 128.1024a11.1616 11.1616 0 1 0 22.3744 0 11.1616 11.1616 0 0 0-22.3744 0z m-167.3216 65.8944a16.7936 16.7936 0 1 0 33.6384 0 16.7936 16.7936 0 0 0-33.6384 0zM363.1616 921.6a22.3744 22.3744 0 1 0 44.7488 0 22.3744 22.3744 0 0 0-44.7488 0z m-159.744-82.0224a28.0064 28.0064 0 1 0 55.9616 0 28.0064 28.0064 0 0 0-56.0128 0zM92.672 700.16a33.6384 33.6384 0 1 0 67.2256 0 33.6384 33.6384 0 0 0-67.2256 0zM51.2 528.9984a39.168 39.168 0 1 0 78.336 0 39.168 39.168 0 0 0-78.336 0z m34.1504-170.0864a44.8 44.8 0 1 0 89.6 0 44.8 44.8 0 0 0-89.6 0zM187.904 221.7984a50.432 50.432 0 1 0 100.864 0 50.432 50.432 0 0 0-100.864 0zM338.432 143.36a55.9616 55.9616 0 1 0 111.9232 0 55.9616 55.9616 0 0 0-111.9744 0z m169.0112-4.9152a61.5936 61.5936 0 1 0 123.2384 0 61.5936 61.5936 0 0 0-123.2384 0z m154.7776 69.632a67.1744 67.1744 0 1 0 134.3488 0 67.1744 67.1744 0 0 0-134.3488 0z m110.0288 130.816a72.8064 72.8064 0 1 0 145.5616 0 72.8064 72.8064 0 0 0-145.5616 0z m43.7248 169.472a78.3872 78.3872 0 1 0 156.8256 0 78.3872 78.3872 0 0 0-156.8256 0z" fill="" ></path></symbol><symbol id="v3-img-right" viewBox="0 0 1024 1024"><path d="M884.808356 96.170317c0-17.780969-16.723893-32.209586-37.309744-32.209586-20.586874 0-37.310767 14.428617-37.310767 32.209586V927.20035c0 17.780969 16.723893 32.209586 37.310767 32.209586 20.585851 0 37.309744-14.428617 37.309744-32.209586V96.170317zM263.249101 159.859871c-27.619034-21.278629-50.027393-11.659551-50.027393 22.116717v659.490146c0 33.44881 22.481014 43.322691 50.027393 22.116716l407.352615-313.385476c27.65485-21.242813 27.618011-55.784514 0-76.989465L263.249101 159.859871z m449.327612 260.298398c60.265572 48.751331 60.301388 134.302799 0 183.090968L297.061185 939.297876c-70.976518 57.422827-158.459988-25.322735-158.459989-116.158556V200.267163c0-91.12644 87.445608-173.6172 158.459989-116.158557l415.515528 336.049663zM297.061185 84.10963"  ></path></symbol><symbol id="v3-img-left" viewBox="0 0 1024 1024"><path d="M138.60222 96.170317V927.20035c0 17.780969 16.723893 32.209586 37.309744 32.209586 20.586874 0 37.310767-14.428617 37.310767-32.209586V96.170317c0-17.780969-16.723893-32.209586-37.310767-32.209586-20.585851 0-37.309744 14.428617-37.309744 32.209586z m621.560278 63.689554L352.80886 473.208509c-27.618011 21.205974-27.65485 55.747675 0 76.989465l407.353638 313.385476c27.54638 21.205974 50.027393 11.33107 50.027393-22.116716v-659.490146c-0.001023-33.776268-22.408359-43.395346-50.027393-22.116717z m-33.812084-75.750241c71.013357-57.459666 158.458965 25.032116 158.458965 116.157533v622.872157c0 90.835821-87.482447 173.581384-158.459988 116.158556L310.834886 603.249237c-60.301388-48.78817-60.265572-134.339638 0-183.090968L726.350414 84.10963z m0 0"  ></path></symbol><symbol id="v3-img-zoom-out" viewBox="0 0 1024 1024"><path d="M919.264 905.984l-138.912-138.912C851.808 692.32 896 591.328 896 480c0-229.376-186.624-416-416-416S64 250.624 64 480s186.624 416 416 416c95.008 0 182.432-32.384 252.544-86.208l141.44 141.44a31.904 31.904 0 0 0 45.248 0 32 32 0 0 0 0.032-45.248zM128 480C128 285.92 285.92 128 480 128s352 157.92 352 352-157.92 352-352 352S128 674.08 128 480z"  ></path><path d="M625.792 448H336a32 32 0 0 0 0 64h289.792a32 32 0 1 0 0-64z"  ></path></symbol><symbol id="v3-img-zoom-big" viewBox="0 0 1024 1024"><path d="M919.264 905.984l-138.912-138.912C851.808 692.32 896 591.328 896 480c0-229.376-186.624-416-416-416S64 250.624 64 480s186.624 416 416 416c95.008 0 182.432-32.384 252.544-86.208l141.44 141.44a31.904 31.904 0 0 0 45.248 0 32 32 0 0 0 0.032-45.248zM128 480C128 285.92 285.92 128 480 128s352 157.92 352 352-157.92 352-352 352S128 674.08 128 480z"  ></path><path d="M625.792 448H512v-112a32 32 0 0 0-64 0V448h-112a32 32 0 0 0 0 64H448v112a32 32 0 1 0 64 0V512h113.792a32 32 0 1 0 0-64z"  ></path></symbol><symbol id="v3-img-rotate-left" viewBox="0 0 1024 1024"><path d="M884.565333 431.914667c-3.114667 36.266667-22.784 55.893333-59.008 59.008-35.2-3.114667-54.357333-22.741333-57.472-59.008 3.114667-35.157333 22.272-54.314667 57.472-57.429334 36.224 3.114667 55.893333 22.272 59.008 57.429334z m-97.834666 0c3.114667 24.874667 16.042667 38.826667 38.826666 41.941333 22.741333-2.048 35.712-16.042667 38.826667-41.941333-3.114667-24.832-16.085333-38.272-38.826667-40.362667-22.784 3.114667-35.712 16.597333-38.826666 40.362667z m-59.008 82.346666c-1.024 103.466667-34.688 156.245333-100.906667 158.293334-69.376-1.024-104.021333-53.76-104.021333-158.293334 0.981333-104.576 35.157333-158.421333 102.442666-161.536 67.285333 2.133333 101.461333 55.893333 102.485334 161.493334z m-169.216 0c0 89.002667 22.229333 132.992 66.730666 131.925334 44.501333 1.066667 66.773333-42.922667 66.773334-131.968 0-90.026667-22.784-134.528-68.309334-133.546667-42.453333 3.114667-64.213333 47.658667-65.194666 133.546667z m-184.746667 49.664c-58.026667-5.205333-89.6-39.338667-94.72-102.485333 4.096-67.285333 37.76-103.509333 100.906667-108.714667 72.448-0.981333 108.16 50.218667 107.093333 153.728 0 109.738667-37.76 165.12-113.322667 166.144-45.568-2.090667-74.538667-24.32-86.954666-66.773333l32.597333-10.88c7.253333 36.266667 26.453333 53.845333 57.472 52.821333 48.64-2.090667 73.472-45.056 74.538667-128.853333-15.530667 29.994667-41.429333 45.013333-77.653334 45.013333z m3.072-181.674666c-39.338667 3.114667-60.586667 28.458667-63.658667 76.074666 2.048 47.616 23.296 72.96 63.658667 76.074667 40.362667 0 65.194667-18.645333 74.538667-55.893333 0-65.194667-24.874667-97.28-74.538667-96.256z m135.594667 641.578666C230.186667 1023.829333 0.597333 794.24 0.597333 512v-0.981333h53.973334v0.981333c0 252.501333 205.354667 457.898667 457.813333 457.898667 252.501333 0 457.898667-205.397333 457.898667-457.898667 0-252.458667-205.397333-457.813333-457.856-457.813333A457.088 457.088 0 0 0 159.530667 220.586667l120.533333-0.938667h8.106667l-47.061334 47.786667-167.936 1.365333-1.109333-167.936 47.189333-47.872v131.626667A510.933333 510.933333 0 0 1 512.426667 0.128C794.624 0.128 1024.256 229.76 1024.256 512c0 282.282667-229.632 511.872-511.829333 511.872z"  ></path></symbol><symbol id="v3-img-antetype" viewBox="0 0 1024 1024"><path d="M316 672h60c4.4 0 8-3.6 8-8V360c0-4.4-3.6-8-8-8h-60c-4.4 0-8 3.6-8 8v304c0 4.4 3.6 8 8 8zM512 622c22.1 0 40-17.9 40-39 0-23.1-17.9-41-40-41s-40 17.9-40 41c0 21.1 17.9 39 40 39zM512 482c22.1 0 40-17.9 40-39 0-23.1-17.9-41-40-41s-40 17.9-40 41c0 21.1 17.9 39 40 39z"  ></path><path d="M880 112H144c-17.7 0-32 14.3-32 32v736c0 17.7 14.3 32 32 32h736c17.7 0 32-14.3 32-32V144c0-17.7-14.3-32-32-32z m-40 728H184V184h656v656z"  ></path><path d="M648 672h60c4.4 0 8-3.6 8-8V360c0-4.4-3.6-8-8-8h-60c-4.4 0-8 3.6-8 8v304c0 4.4 3.6 8 8 8z"  ></path></symbol><symbol id="v3-img-rotate-right" viewBox="0 0 1024 1024"><path d="M375.381 562.517c-57.898-5.162-89.472-39.296-94.634-102.4 4.138-67.2 37.76-103.381 100.864-108.586 72.362-1.024 108.032 50.176 107.008 153.6 0 109.61-37.76 164.906-113.238 165.973-45.525-2.133-74.453-24.32-86.869-66.73l32.597-10.838c7.211 36.181 26.368 53.76 57.387 52.736 48.597-2.048 73.387-44.97 74.453-128.768-15.488 29.995-41.386 45.013-77.568 45.013z m3.115-181.504c-39.296 3.072-60.501 28.459-63.573 75.99 2.048 47.573 23.253 72.96 63.573 76.032 40.32 0 65.152-18.603 74.453-55.851 0-65.152-24.832-97.195-74.453-96.17z m145.835 131.84c0.981-104.448 35.114-158.208 102.4-161.322 67.157 2.09 101.29 55.85 102.357 161.322-1.067 103.424-34.688 156.16-100.821 158.251-69.334-1.067-103.936-53.76-103.936-158.25z m169.088 0c0-89.984-22.784-134.4-68.267-133.418-42.41 3.114-64.128 47.616-65.152 133.418 0 88.96 22.187 132.907 66.688 131.84 44.459 1.067 66.73-42.88 66.73-131.84z m75.989-82.218c3.115-35.158 22.23-54.272 57.387-57.387 36.181 3.115 55.893 22.23 58.965 57.387-3.115 36.224-22.784 55.893-58.965 58.965-35.158-3.115-54.272-22.741-57.387-58.965z m96.17 0c-3.071-24.832-16.042-38.23-38.783-40.32-22.742 3.114-35.67 16.554-38.784 40.32 3.114 24.832 16.042 38.826 38.826 41.898 22.699-2.048 35.67-16.042 38.742-41.898z m-354.175 592.64c-281.984 0-511.36-229.376-511.36-511.403C0 229.973 229.419 0.512 511.403 0.512c157.696 0 298.922 71.765 392.789 184.32V54.613l47.147 47.872-1.11 167.766-167.765-1.366-47.061-47.744h8.106l121.558 0.982a456.704 456.704 0 0 0-353.664-167.68c-252.246 0-457.43 205.226-457.43 457.429 0 252.288 205.184 457.515 457.43 457.515S968.875 764.16 968.875 511.872v-0.981h53.888v0.981c0 282.027-229.376 511.403-511.36 511.403z"  ></path></symbol></svg>',D=(D=document.getElementsByTagName("script"))[D.length-1].getAttribute("data-injectcss");if(D&&!i.__iconfont__svg__cssinject__){i.__iconfont__svg__cssinject__=!0;try{document.write("<style>.svgfont {display: inline-block;width: 1em;height: 1em;fill: currentColor;vertical-align: -0.1em;font-size:16px;}</style>")}catch(C){console&&console.log(C)}}function q(){P||(P=!0,L())}g=function(){var C,h=document.createElement("div");h.innerHTML=U,U=null,(h=h.getElementsByTagName("svg")[0])&&(h.setAttribute("aria-hidden","true"),h.style.position="absolute",h.style.width=0,h.style.height=0,h.style.overflow="hidden",h=h,(C=document.body).firstChild?function(c3,Q){Q.parentNode.insertBefore(c3,Q)}(h,C.firstChild):C.appendChild(h))},document.addEventListener?~["complete","loaded","interactive"].indexOf(document.readyState)?setTimeout(g,0):(_=function(){document.removeEventListener("DOMContentLoaded",_,!1),g()},document.addEventListener("DOMContentLoaded",_,!1)):document.attachEvent&&(L=g,S=i.document,P=!1,function C(){try{S.documentElement.doScroll("left")}catch{return void setTimeout(C,50)}q()}(),S.onreadystatechange=function(){S.readyState=="complete"&&(S.onreadystatechange=null,q())})}(typeof window<"u"?window:global),z()}),Object.assign(Object.assign(Object.assign({vImagesWrap:s,imgContainer:r},Y(t)),Y(c)),{handleClose:B,toggleImg:M,initImgSize:w,dragStyle:l,handleScale:n,handleRotate:A,visibleArrowBtn:m,isMultiple:u})}});const y=e=>(p3("data-v-15ccadb3"),e=e(),h3(),e),P3={key:0,class:"v-images-wrap",ref:"vImagesWrap"},D3={class:"icon img-loading rotate-animation","aria-hidden":"true"},R3=[y(()=>p("use",{"xlink:href":"#v3-img-loading"},null,-1))],N3=["src"],$3=[y(()=>p("use",{"xlink:href":"#v3-img-img-error"},null,-1))],W3=[y(()=>p("use",{"xlink:href":"#v3-img-close"},null,-1))],H3=[y(()=>p("svg",{class:"icon","aria-hidden":"true"},[p("use",{"xlink:href":"#v3-img-left"})],-1))],Y3=[y(()=>p("svg",{class:"icon","aria-hidden":"true"},[p("use",{"xlink:href":"#v3-img-right"})],-1))],F3={key:3,class:"v3-img-preview-toolbar"},G3=[y(()=>p("use",{"xlink:href":"#v3-img-zoom-out"},null,-1))],U3=[y(()=>p("use",{"xlink:href":"#v3-img-zoom-big"},null,-1))],q3=[y(()=>p("use",{"xlink:href":"#v3-img-antetype"},null,-1))],Q3=[y(()=>p("use",{"xlink:href":"#v3-img-rotate-left"},null,-1))],X3=[y(()=>p("use",{"xlink:href":"#v3-img-rotate-right"},null,-1))];(function(e,a){a===void 0&&(a={});var o=a.insertAt;if(e&&typeof document<"u"){var s=document.head||document.getElementsByTagName("head")[0],r=document.createElement("style");r.type="text/css",o==="top"&&s.firstChild?s.insertBefore(r,s.firstChild):s.appendChild(r),r.styleSheet?r.styleSheet.cssText=e:r.appendChild(document.createTextNode(e))}})(`.icon[data-v-15ccadb3] {
  width: 1em;
  height: 1em;
  vertical-align: -0.15em;
  fill: currentColor;
  overflow: hidden;
}
.v-images-wrap[data-v-15ccadb3] {
  z-index: 200;
  user-select: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  background: rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(3px);
  color: #fff;
}
.v-images-wrap[data-v-15ccadb3] .img-loading[data-v-15ccadb3],
.v-images-wrap[data-v-15ccadb3] .img-content[data-v-15ccadb3] {
  font-size: 50px;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  margin: auto;
  transition: all 0.2s;
}
.v-images-wrap[data-v-15ccadb3] .img-container[data-v-15ccadb3] {
  z-index: 201;
  position: absolute;
  height: 100vh;
  width: 100vw;
  top: 0;
  left: 0;
  text-align: center;
}
.v-images-wrap[data-v-15ccadb3] .img-container[data-v-15ccadb3] .img-content[data-v-15ccadb3] {
  max-width: 100%;
  max-height: 100%;
}
.v-images-wrap[data-v-15ccadb3] .img-container[data-v-15ccadb3] .img-error[data-v-15ccadb3] {
  font-size: 300px;
  color: #d8d8d8;
}
.v-images-wrap[data-v-15ccadb3] .rotate-animation[data-v-15ccadb3] {
  animation: rotate-15ccadb3 1.5s linear infinite;
}
.v-images-wrap[data-v-15ccadb3] .arrow[data-v-15ccadb3] {
  width: 42px;
  height: 42px;
  text-align: center;
  line-height: 42px;
  position: absolute;
  top: 50%;
  border-radius: 50%;
  transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  font-size: 24px;
  cursor: pointer;
  transition: all 0.2s;
  z-index: 280;
  background: rgba(0, 0, 0, 0.3);
}
.v-images-wrap[data-v-15ccadb3] .arrow[data-v-15ccadb3][data-v-15ccadb3]:hover {
  opacity: 0.8;
  transform: translateY(-50%) scale(1.2);
}
.v-images-wrap[data-v-15ccadb3] .arrow-left[data-v-15ccadb3] {
  left: 50px;
}
.v-images-wrap[data-v-15ccadb3] .arrow-right[data-v-15ccadb3] {
  right: 50px;
}
.v-images-wrap[data-v-15ccadb3] .close-btn[data-v-15ccadb3] {
  z-index: 205;
  position: absolute;
  right: 50px;
  top: 50px;
  width: 36px;
  height: 36px;
  font-size: 22px;
  line-height: 36px;
  text-align: center;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.2s;
  color: #e7e5e5;
  background: rgba(0, 0, 0, 0.3);
}
.v-images-wrap[data-v-15ccadb3] .close-btn[data-v-15ccadb3][data-v-15ccadb3]:hover {
  opacity: 0.8;
  transform: scale(1.2);
}
.v-images-wrap[data-v-15ccadb3] .v3-img-preview-toolbar[data-v-15ccadb3] {
  z-index: 205;
  position: absolute;
  bottom: 10%;
  font-size: 26px;
  width: 100%;
  display: flex;
  justify-content: center;
  cursor: pointer;
}
.v-images-wrap[data-v-15ccadb3] .v3-img-preview-toolbar[data-v-15ccadb3] section[data-v-15ccadb3] {
  height: 44px;
  bottom: 10%;
  padding: 0 22px;
  display: flex;
  align-items: center;
  border-radius: 22px;
  background: rgba(0, 0, 0, 0.3);
  color: #c3c3c3;
}
.v-images-wrap[data-v-15ccadb3] .v3-img-preview-toolbar[data-v-15ccadb3] section[data-v-15ccadb3] svg[data-v-15ccadb3] {
  box-sizing: content-box;
  padding: 0 10px;
  transition: all 0.2s;
}
.v-images-wrap[data-v-15ccadb3] .v3-img-preview-toolbar[data-v-15ccadb3] section[data-v-15ccadb3] svg[data-v-15ccadb3][data-v-15ccadb3]:hover {
  transform: scale(1.2);
}
@keyframes rotate-15ccadb3 {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
`),V.render=function(e,a,o,s,r,l){return f(),s3(m3,{name:"fade"},{default:d3(()=>[e.visible?(f(),b("div",P3,[R((f(),b("svg",D3,R3,512)),[[N,e.imgState==="loading"]]),p("div",{ref:"imgContainer",style:$(e.dragStyle),class:"img-container"},[R(p("img",{class:"img-content",onDragstart:a[0]||(a[0]=W(()=>{},["prevent"])),src:e.src,style:$(`transform: scale(${e.imgScale}) rotate(${e.imgRotate}deg);`),alt:""},null,44,N3),[[N,e.imgState==="success"]]),R((f(),b("svg",{class:"icon img-content img-error",onDragstart:a[1]||(a[1]=W(()=>{},["prevent"])),"aria-hidden":"true",style:$(`transform: scale(${e.imgScale}) rotate(${e.imgRotate}deg);`)},$3,36)),[[N,e.imgState==="error"]])],4),e.showCloseBtn?(f(),b("svg",{key:0,class:"icon close-btn","aria-hidden":"true",onClick:a[2]||(a[2]=W(function(){return e.handleClose&&e.handleClose(...arguments)},["stop"]))},W3)):I("",!0),e.visibleArrowBtn?(f(),b("div",{key:1,class:"arrow arrow-left",onClick:a[3]||(a[3]=t=>e.toggleImg(!1))},H3)):I("",!0),e.visibleArrowBtn?(f(),b("div",{key:2,class:"arrow arrow-right",onClick:a[4]||(a[4]=t=>e.toggleImg(!0))},Y3)):I("",!0),e.showToolbar?(f(),b("div",F3,[p("section",null,[(f(),b("svg",{class:"icon","aria-hidden":"true",onClick:a[5]||(a[5]=t=>e.handleScale(-.1,!1))},G3)),(f(),b("svg",{class:"icon","aria-hidden":"true",onClick:a[6]||(a[6]=t=>e.handleScale(.1,!1))},U3)),(f(),b("svg",{class:"icon","aria-hidden":"true",onClick:a[7]||(a[7]=function(){return e.initImgSize&&e.initImgSize(...arguments)})},q3)),(f(),b("svg",{class:"icon","aria-hidden":"true",onClick:a[8]||(a[8]=t=>e.handleRotate(!1))},Q3)),(f(),b("svg",{class:"icon","aria-hidden":"true",onClick:a[9]||(a[9]=t=>e.handleRotate(!0))},X3))])])):I("",!0)],512)):I("",!0)]),_:1})},V.__scopeId="data-v-15ccadb3";let l3={};const r3="v3-img-preview-container-id";let O={};function K3(e){if(O._instance)return!1;Array.isArray(e)&&(e={images:e}),typeof e=="string"&&(e={url:e}),e=Object.assign(Object.assign({},l3),e);let a=document.getElementById(r3);return a||(a=document.createElement("div"),a.id=r3,document.body.appendChild(a)),O=v3(V,Object.assign(Object.assign({},e),{on_unmount_v3_ima_preview_app:()=>O.unmount()})),O.mount(a),O}var Z3=(()=>{const e=V;return e.install=(a,o)=>{l3=o,a.config.globalProperties.$v3ImgPreviewFn=K3},e})();export{Z3 as default,K3 as v3ImgPreviewFn};
