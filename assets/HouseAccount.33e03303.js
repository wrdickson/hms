import{i as t,z as n,j as c,_ as s,g as u,r,o as i}from"./index.33301253.js";import{F as a}from"./FolioView.e9608674.js";import"./resView.65ec67a9.js";const l={name:"HouseAccountVue",props:[],components:{FolioView:a},data(){return{componentKey:1}},computed:{token(){return t().token},houseAccount(){return n().autoloadOptions.house_account_folio.option_value}},methods:{getHouseAccount(){c.folios.getFolio1(this.token,this.houseAccount).then(o=>{console.log(o)})}},mounted(){this.getHouseAccount()}};function p(o,m){const e=r("FolioView");return i(),u(e,{folioId:o.houseAccount,selectedReservation:"false",componentKey:o.componentKey},null,8,["folioId","componentKey"])}const h=s(l,[["render",p]]);export{h as default};