(window.webpackJsonp=window.webpackJsonp||[]).push([[2],{201:function(a,t,e){"use strict";e.r(t);var n={data:function(){return{dialog:!1}}},r=e(25),i=e(26),o=e.n(i),s=e(119),l=e(22),c=e(24),d=e(203),v=e(139),u=e(128),p=Object(r.a)(n,(function(){var a=this,t=a.$createElement,e=a._self._c||t;return e("v-dialog",{attrs:{width:"500"},scopedSlots:a._u([{key:"activator",fn:function(t){var n=t.on,r=t.attrs;return[e("v-btn",a._g(a._b({attrs:{color:"red lighten-2",dark:""}},"v-btn",r,!1),n),[a._v("\n            Open Dialog\n        ")])]}}]),model:{value:a.dialog,callback:function(t){a.dialog=t},expression:"dialog"}},[a._v(" "),e("v-card",[e("v-card-title",{staticClass:"headline grey lighten-2"},[a._v("\n            Privacy Policy\n        ")]),a._v(" "),e("v-card-text",[a._v("\n            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do\n            eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut\n            enim ad minim veniam, quis nostrud exercitation ullamco laboris\n            nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor\n            in reprehenderit in voluptate velit esse cillum dolore eu fugiat\n            nulla pariatur. Excepteur sint occaecat cupidatat non proident,\n            sunt in culpa qui officia deserunt mollit anim id est laborum.\n        ")]),a._v(" "),e("v-divider"),a._v(" "),e("v-card-actions",[e("v-spacer"),a._v(" "),e("v-btn",{attrs:{color:"primary",text:""},on:{click:function(t){a.dialog=!1}}},[a._v("\n                I accept\n            ")])],1)],1)],1)}),[],!1,null,null,null),g=p.exports;o()(p,{VBtn:s.a,VCard:l.a,VCardActions:c.a,VCardText:c.b,VCardTitle:c.c,VDialog:d.a,VDivider:v.a,VSpacer:u.a});var m={components:{"test-dialog":g},data:function(){return{headers:[{text:"Dessert (100g serving)",align:"start",sortable:!1,value:"name"},{text:"Calories",value:"calories"},{text:"Fat (g)",value:"fat"},{text:"Carbs (g)",value:"carbs"},{text:"Protein (g)",value:"protein"},{text:"Iron (%)",value:"iron"}],desserts:[],loading:!0}},methods:{stopLoading:function(){this.loading=!1,this.desserts=[{name:"Frozen Yogurt",calories:159,fat:6,carbs:24,protein:4,iron:"1%"},{name:"Ice cream sandwich",calories:237,fat:9,carbs:37,protein:4.3,iron:"1%"},{name:"Eclair",calories:262,fat:16,carbs:23,protein:6,iron:"7%"},{name:"Cupcake",calories:305,fat:3.7,carbs:67,protein:4.3,iron:"8%"},{name:"Gingerbread",calories:356,fat:16,carbs:49,protein:3.9,iron:"16%"},{name:"Jelly bean",calories:375,fat:0,carbs:94,protein:0,iron:"0%"},{name:"Lollipop",calories:392,fat:.2,carbs:98,protein:0,iron:"2%"},{name:"Honeycomb",calories:408,fat:3.2,carbs:87,protein:6.5,iron:"45%"},{name:"Donut",calories:452,fat:25,carbs:51,protein:4.9,iron:"22%"},{name:"KitKat",calories:518,fat:26,carbs:65,protein:7,iron:"6%"}]},startLoading:function(){this.loading=!0,this.desserts=[]}}},b=e(197),f=e(129),_=e(200),h=e(141),x=e(198),V=e(199),k=Object(r.a)(m,(function(){var a=this,t=a.$createElement,e=a._self._c||t;return e("v-container",[e("v-row",[e("v-col",[e("v-card",[e("v-container",[e("v-btn",{attrs:{color:"green",elevation:"2",dark:""},on:{click:a.startLoading}},[a._v("Start Loading")]),a._v(" "),e("v-btn",{attrs:{color:"pink",elevation:"2",dark:""},on:{click:a.stopLoading}},[a._v("Stop Loading")]),a._v(" "),e("test-dialog")],1),a._v(" "),e("v-container",[e("v-btn",{attrs:{color:"primary",elevation:"2",icon:"",loading:a.loading}},[e("v-icon",[a._v("refresh")])],1)],1)],1)],1)],1),a._v(" "),e("v-row",[e("v-col",[e("v-skeleton-loader",{attrs:{type:"table",loading:a.loading,transition:"fade-transition"}},[e("v-data-table",{staticClass:"elevation-1",attrs:{headers:a.headers,items:a.desserts,"items-per-page":5,loading:a.loading,"loading-text":"A carregar"}})],1)],1)],1),a._v(" "),e("v-row",[e("v-col",[e("v-container",[e("v-data-table",{staticClass:"elevation-1",attrs:{headers:a.headers,items:a.desserts,"items-per-page":5,loading:a.loading,"loading-text":"A carregar"}})],1)],1)],1)],1)}),[],!1,null,null,null);t.default=k.exports;o()(k,{VBtn:s.a,VCard:l.a,VCol:b.a,VContainer:f.a,VDataTable:_.a,VIcon:h.a,VRow:x.a,VSkeletonLoader:V.a})}}]);