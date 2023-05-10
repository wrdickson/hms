import{u as V,f as m,n as C,_ as g,o as n,c as h,g as y,w as i,b as l,F as w,h as v,m as S,e as b,r,v as G,d as c,t as _,i as I,j as x,s as K,E as $,k as A}from"./index.33301253.js";import{r as R}from"./resView.65ec67a9.js";const L={name:"ProcessSaleType",props:["saleType","processSaleTypeResetKey"],emits:["process-sale-type:add-sale-item"],data(){return{description:"",saleQuantities:[-5,-4,-3,-2,-1,1,2,3,4,5],iSale:null}},computed:{subtotal(){return Number(this.iSale.price*this.iSale.quantity).toFixed(2)},saleTotal(){return this.iSale&&this.subtotal&&this.totalTax?Number(Number(this.subtotal)+Number(this.totalTax)).toFixed(2):null},saleTypes(){return V().saleTypes},taxRate(){if(this.iSale){const e=this.iSale.tax_types;let a=0;return m.each(e,t=>{const s=m.find(this.taxTypes,o=>o.id==t);a=Number(a)+Number(s.tax_rate)}),Number(a)}else return null},taxSpread(){if(this.iSale&&this.subtotal){let e=[];return m.each(this.iSale.tax_types,a=>{let t={};const s=m.find(this.taxTypes,o=>o.id==a);t.i=a,t.r=s.tax_rate,t.t=Number(s.tax_rate*this.subtotal).toFixed(2),e.push(t)}),e}else return null},taxTypes(){return C().taxTypes},totalTax(){return this.taxRate!=null?(this.subtotal*Number(this.taxRate)).toFixed(2):null}},methods:{addToSale(){const e={saleType:this.iSale.id,saleTitle:this.iSale.title,saleQuantity:this.iSale.quantity,salePrice:this.iSale.price,saleSubtotal:this.subtotal,saleTax:this.totalTax,saleTotal:this.saleTotal,taxTypes:this.iSale.tax_types,taxSpread:this.taxSpread,description:this.iSale.description};this.$emit("process-sale-type:add-sale-item",e)}},watch:{saleType(e){console.log("watch on process sale type",e),e?(e.quantity=1,e.description=e.title,e.is_fixed_price?e.price=e.fixed_price:e.price=null,this.iSale=e):this.iSale=null}}},O={class:"process-sale-wrapper"};function z(e,a){const t=r("el-input"),s=r("el-form-item"),o=r("el-option"),p=r("el-select"),u=r("el-form"),f=r("el-button");return n(),h("div",O,[e.iSale?(n(),y(u,{key:0,size:"small",inline:!0,"label-position":"top"},{default:i(()=>[l(s,{label:"Type"},{default:i(()=>[l(t,{disabled:"",modelValue:e.iSale.title,"onUpdate:modelValue":a[0]||(a[0]=d=>e.iSale.title=d)},null,8,["modelValue"])]),_:1}),l(s,{label:"Qty",style:{width:"60px"}},{default:i(()=>[l(p,{modelValue:e.iSale.quantity,"onUpdate:modelValue":a[1]||(a[1]=d=>e.iSale.quantity=d)},{default:i(()=>[(n(!0),h(w,null,v(e.saleQuantities,d=>(n(),y(o,{label:d,value:d},null,8,["label","value"]))),256))]),_:1},8,["modelValue"])]),_:1}),Boolean(Number(e.iSale.is_fixed_price))?(n(),y(s,{key:0,label:"Amount",style:{width:"80px"}},{default:i(()=>[l(t,{disabled:"",modelValue:e.iSale.price,"onUpdate:modelValue":a[2]||(a[2]=d=>e.iSale.price=d)},null,8,["modelValue"])]),_:1})):S("",!0),Boolean(Number(e.iSale.is_fixed_price))?S("",!0):(n(),y(s,{key:1,label:"Amount",style:{width:"80px"}},{default:i(()=>[l(t,{modelValue:e.iSale.price,"onUpdate:modelValue":a[3]||(a[3]=d=>e.iSale.price=d)},null,8,["modelValue"])]),_:1})),l(s,{label:"Subtotal",style:{width:"80px"}},{default:i(()=>[l(t,{disabled:"",modelValue:e.subtotal,"onUpdate:modelValue":a[4]||(a[4]=d=>e.subtotal=d)},null,8,["modelValue"])]),_:1}),l(s,{label:"Tax",style:{width:"80px"}},{default:i(()=>[l(t,{disabled:"",modelValue:e.totalTax,"onUpdate:modelValue":a[5]||(a[5]=d=>e.totalTax=d)},null,8,["modelValue"])]),_:1}),l(s,{label:"Total",style:{width:"80px"}},{default:i(()=>[l(t,{disabled:"",modelValue:e.saleTotal,"onUpdate:modelValue":a[6]||(a[6]=d=>e.saleTotal=d)},null,8,["modelValue"])]),_:1})]),_:1})):S("",!0),e.iSale?(n(),y(u,{key:1,size:"small"},{default:i(()=>[l(s,{label:"Description"},{default:i(()=>[l(t,{modelValue:e.iSale.description,"onUpdate:modelValue":a[7]||(a[7]=d=>e.iSale.description=d)},null,8,["modelValue"])]),_:1})]),_:1})):S("",!0),e.saleTotal&&e.saleTotal>0||e.saleTotal<0?(n(),y(u,{key:2},{default:i(()=>[l(s,null,{default:i(()=>[l(f,{onClick:e.addToSale,type:"success"},{default:i(()=>[b("Add to sale")]),_:1},8,["onClick"])]),_:1})]),_:1})):S("",!0)])}const B=g(L,[["render",z]]),q={name:"SaleItems",props:["saleItems"],emits:["sale-items:remove-at-index"],data(){return{}},computed:{saleItemsExist(){return this.saleItems.length>0}},methods:{handleRemoveItem(e){console.log(e),this.$emit("sale-items:remove-at-index",e.$index)}},watch:{saleItems(e){console.log("new prop on saleItems component",e)}}};function J(e,a,t,s,o,p){const u=r("el-table-column"),f=r("el-button"),d=r("el-table");return n(),y(d,{size:"small",data:t.saleItems,style:{width:"100%"}},{default:i(()=>[l(u,{prop:"saleTitle",width:"140",label:"Type"}),l(u,{prop:"description",label:"Description"}),l(u,{prop:"saleQuantity",width:"40",label:"Qty"}),l(u,{prop:"salePrice",width:"60",label:"Price"}),l(u,{prop:"saleSubtotal",width:"80",label:"Subtotal"}),l(u,{prop:"saleTax",width:"60",label:"Tax"}),l(u,{prop:"saleTotal",width:"60",label:"Total"}),l(u,{width:"40"},{default:i(T=>[l(f,{onClick:k=>p.handleRemoveItem(T),type:"danger",size:"small"},{default:i(()=>[b("X")]),_:2},1032,["onClick"])]),_:1})]),_:1},8,["data"])}const Q=g(q,[["render",J]]),U={name:"CompleteSale",props:["saleItems"],emits:["compelete-sale:payment-type-selected"],data(){return{selectedPaymentType:null,testMode:!1}},computed:{activePaymentTypes(){return G().activePaymentTypes},saleItemsLength(){return this.saleItems.length},saleTotal(){let e=0;return m.each(this.saleItems,a=>{e+=Number(a.saleTotal)}),Number(e).toFixed(2)}},methods:{paymentTypeSelected(){console.log("p",this.selectedPaymentType),this.$emit("compelete-sale:payment-type-selected",this.selectedPaymentType)}}},j={key:0},E=c("hr",null,null,-1),M={style:{display:"flex"}};function H(e,a,t,s,o,p){const u=r("el-option"),f=r("el-select"),d=r("el-button");return n(),h(w,null,[o.testMode?(n(),h("div",j,[c("div",null,"sil: "+_(p.saleItemsLength),1),c("div",null,"t: "+_(p.saleTotal),1)])):S("",!0),E,c("div",M,[c("span",null,"Total: "+_(p.saleTotal),1),l(f,{style:{"margin-left":"auto"},modelValue:o.selectedPaymentType,"onUpdate:modelValue":a[0]||(a[0]=T=>o.selectedPaymentType=T)},{default:i(()=>[(n(!0),h(w,null,v(p.activePaymentTypes,T=>(n(),y(u,{value:T.id,label:T.payment_title},null,8,["value","label"]))),256))]),_:1},8,["modelValue"]),o.selectedPaymentType?(n(),y(d,{key:0,onClick:p.paymentTypeSelected,type:"success",style:{"margin-left":"auto"}},{default:i(()=>[b("CompleteSale")]),_:1},8,["onClick"])):(n(),y(d,{key:1,disabled:"",type:"success",style:{"margin-left":"auto"}},{default:i(()=>[b("CompleteSale")]),_:1}))])],64)}const X=g(U,[["render",H]]),W={name:"FolioDisplay",props:["folioId","folioViewerReloadKey"],data(){return{folioDataLoaded:!1,folioData:[]}},computed:{allSaleItems(){let e=[];return m.each(this.folioData.sale_items,a=>{e.push(a)}),e},allSalePayments(){let e=[];return m.each(this.folioData.payments,a=>{e.push(a)}),e},token(){return I().token}},methods:{getFolio1(){x.folios.getFolio1(this.token,this.folioId).then(e=>{this.folioData=e.data.folio_to_array,this.folioDataLoaded=!0})}},created(){this.getFolio1()},watch:{folioViewerReloadKey(e){console.log("foliodispaly gets reload key"),this.folioData=[],this.folioDataLoaded=!1,this.getFolio1()}}},Y={key:0,class:"folio-display-wrapper"},Z=c("div",{style:{"font-size":"1.5em"}},"Sale Items",-1),ee=c("hr",null,null,-1),te=c("div",{style:{"font-size":"1.5em"}},"Payments",-1);function le(e,a){const t=r("el-table-column"),s=r("el-table");return e.folioDataLoaded?(n(),h("div",Y,[Z,l(s,{size:"small",data:e.allSaleItems,"max-height":"250",style:{width:"100%"},stripe:""},{default:i(()=>[l(t,{prop:"sale_datetime",width:"143",label:"Date"}),l(t,{prop:"description",label:"Description"}),l(t,{prop:"sale_quantity",width:"70",label:"Qty"}),l(t,{prop:"sale_price",width:"70",label:"Price"}),l(t,{prop:"sale_subtotal",width:"70",label:"Subtotal"}),l(t,{prop:"sale_tax",width:"70",label:"Tax"}),l(t,{prop:"sale_total",width:"70",label:"Total"})]),_:1},8,["data"]),ee,te,l(s,{size:"small",data:e.allSalePayments,"max-height":"250",style:{width:"100%"},"show-summary":"",stripe:""},{default:i(()=>[l(t,{prop:"datetime_posted",width:"143",label:"Date"}),l(t,{prop:"total",width:"70",label:"Total"}),l(t,{prop:"payment_type",width:"100",label:"Type"})]),_:1},8,["data"])])):S("",!0)}const ae=g(W,[["render",le]]),se={name:"SaleGroupSelect",emits:["sale-group-select:group-selected"],computed:{groupSet(){let e=[];return m.each(this.saleTypeGroups,a=>{let t={};t.groupTitle=a.title,t.groupId=a.id;let s=m.filter(this.saleTypes,o=>o.sale_type_group==a.id);t.saleTypes=s,e.push(t)}),e},saleTypeGroups(){return K().saleTypeGroups},saleTypes(){return V().saleTypes}},methods:{handleGroupClick(e){console.log("group click",JSON.parse(JSON.stringify(e))),this.$emit("sale-group-select:group-selected",e)}}};function oe(e,a){const t=r("el-button"),s=r("el-button-group");return n(),h("div",null,[l(s,{type:"primary"},{default:i(()=>[(n(!0),h(w,null,v(e.groupSet,o=>(n(),y(t,{size:"large",onClick:p=>e.handleGroupClick(o)},{default:i(()=>[b(_(o.groupTitle),1)]),_:2},1032,["onClick"]))),256))]),_:1})])}const ie=g(se,[["render",oe]]),ne={name:"SaleTypeSelect",props:["selectedGroup"],emits:["sale-type-picker:sale-type-selected"],data(){return{}},methods:{handleSelectSaleType(e){this.$emit("sale-type-picker:sale-type-selected",e)}}};function re(e,a,t,s,o,p){const u=r("el-button"),f=r("el-button-group");return n(),y(f,null,{default:i(()=>[(n(!0),h(w,null,v(t.selectedGroup.saleTypes,d=>(n(),y(u,{type:"primary",plain:"",onClick:T=>p.handleSelectSaleType(d)},{default:i(()=>[b(_(d.title),1)]),_:2},1032,["onClick"]))),256))]),_:1})}const de=g(ne,[["render",re]]),pe={name:"FolioSaleDisplay1",props:["folioId","folioViewerReloadKey"],data(){return{folioDataLoaded:!1,folioDetailData:[]}},computed:{computedFolioData(){let e=[];m.each(this.folioDetailData,t=>{m.includes(e,t.id)||e.push(t.id)});let a=[];return m.each(e,t=>{let s=m.find(this.folioDetailData,o=>t==o.id);a.push({id:s.id,paymentTotal:s.payment_total,date:s.posted_date,postedBy:s.posted_by,paymentType:s.payment_type,saleItems:m.filter(this.folioDetailData,o=>t==o.id)})}),a},token(){return I().token}},methods:{getFolio1(){x.folios.getFolio1(this.token,this.folioId).then(e=>{console.table(e.data.folio_to_array.sale_detail),this.folioDetailData=e.data.folio_to_array.sale_detail,this.folioData=e.data.folio_to_array,this.folioDataLoaded=!0})}},created(){this.getFolio1()},watch:{folioViewerReloadKey(e){this.folioData=[],this.folioDataLoaded=!1,this.getFolio1()}}};const ue={key:0,class:"folio-display-wrapper"},ce={style:{"margin-left":"50px"}};function me(e,a){const t=r("el-table-column"),s=r("el-table");return n(),h(w,null,[b(" FolioSaleDisplay1 "),e.folioDataLoaded?(n(),h("div",ue,[l(s,{"header-cell-class-name":"fsd-1",data:e.computedFolioData,"max-height":"1050",style:{width:"100%"},"row-class-name":"c-row","default-expand-all":!0},{default:i(()=>[l(t,{type:"expand"},{default:i(o=>[c("div",ce,[l(s,{size:"small",border:!0,"cell-class-name":"fsc-cell",width:"100%",data:o.row.saleItems},{default:i(()=>[l(t,{label:"Title",width:"150",prop:"title"}),l(t,{label:"Description",width:"150",prop:"description","show-overflow-tooltip:":"",true:""}),l(t,{label:"Qty",width:"60",prop:"sale_quantity"}),l(t,{label:"Price",width:"80",prop:"sale_price"}),l(t,{label:"Subtotal",width:"80",prop:"sale_subtotal"}),l(t,{label:"Tax",width:"80",prop:"sale_tax"}),l(t,{label:"Total",width:"120",prop:"sale_total"})]),_:2},1032,["data"])])]),_:1}),l(t,{prop:"date",label:"Date"}),l(t,{prop:"postedBy",label:"Posted By"}),l(t,{prop:"paymentType",label:"Payment Type"}),l(t,{prop:"paymentTotal",label:"Amt Paid",width:"120"})]),_:1},8,["data"])])):S("",!0)],64)}const ye=g(pe,[["render",me]]);const _e={name:"FolioView",props:["folioId","selectedReservation","componentKey"],components:{ProcessSaleType:B,SaleItems:Q,CompleteSale:X,FolioViewer:ae,SaleGroupSelect:ie,SaleTypeSelect:de,FolioSaleDisplay1:ye},data(){return{folioViewerReloadKey:1,processSaleTypeResetKey:1,rootSpaces:[],saleItems:[],selectedPaymentType:null,selectedGroup:null,selectedSaleType:null}},computed:{rootSpace(){return m.find(this.rootSpaces,a=>a.id==this.selectedReservation.space_id)},saleSubtotal(){let e=0;return m.each(this.saleItems,a=>{e+=Number(a.saleSubtotal)}),e},saleTaxTotal(){let e=0;return m.each(this.saleItems,a=>{e+=Number(a.saleTax)}),e},saleTotal(){let e=0;return m.each(this.saleItems,a=>{e+=Number(a.saleTotal)}),Number(e).toFixed(2)},token(){return I().token}},methods:{addSaleItem(e){console.log("addSaleItem: ",e),e.taxTypes=Object.values(e.taxTypes),this.saleItems.push(e),this.processSaleTypeResetKey+=1,this.saleTypePickerResetKey+=1,this.selectedSaleType=null,this.selectedGroup=null},groupSelected(e){console.log("setting i",JSON.parse(JSON.stringify(e))),this.selectedGroup=JSON.parse(JSON.stringify(e)),this.selectedSaleType=null},handlePaymentTypeSelected(e){if(this.selectedPaymentType=e,this.saleItems&&this.saleItems.length>0){console.log("here"),this.selectedReservation.id,this.selectedReservation.customer,this.selectedReservation.folioId;const a={resId:this.selectedReservation.id,resCustomer:this.selectedReservation.customer,resFolio:this.folioId,saleItems:this.saleItems,paymentType:e,saleTotal:this.saleTotal,saleSubtotal:this.saleSubtotal,saleTax:this.saleTaxTotal,soldBy:I().account.id};console.log(a),console.log(JSON.stringify(a)),x.payments.postQuickFolioSale(a,this.token).then(t=>{console.log("quick sale",t.data),t.data.sale_items_posted&&t.data.payment_posted&&($({type:"success",message:"Transaction completed"}),this.resetSale(),this.folioViewerReloadKey+=1)})}},removeItemAtIndex(e){console.log("rAI",e),this.saleItems.splice(e,1),this.selectedGroup=null},resetSale(){this.selectedGroup=null,this.selectedSaleType=null,this.saleItems=[]},saleTypeSelected(e){e=JSON.parse(JSON.stringify(e)),this.selectedSaleType=e,console.log(e)}},mounted(){R().showHideRootSpaceCopy?this.rootSpaces=R().showHideRootSpaceCopy:this.rootSpaces=A().rootSpaces},watch:{folioId(e){this.selectedSaleType=null}}},he={key:0},Se=c("span",null," : ",-1),fe={key:0},Te=c("hr",null,null,-1),be=c("hr",null,null,-1);function ge(e,a,t,s,o,p){const u=r("el-button"),f=r("SaleGroupSelect"),d=r("SaleTypeSelect"),T=r("ProcessSaleType"),k=r("SaleItems"),P=r("CompleteSale"),D=r("el-col"),F=r("FolioSaleDisplay1"),N=r("el-row");return n(),h("div",null,[l(N,null,{default:i(()=>[l(D,{span:6},{default:i(()=>[t.selectedReservation.id?(n(),h("div",he,[c("div",null,_(t.selectedReservation.customer_first)+"\xA0"+_(this.selectedReservation.customer_last),1),c("div",null,[c("span",null,_(t.selectedReservation.checkin),1),Se,c("span",null,_(t.selectedReservation.checkout),1)]),p.rootSpace?(n(),h("div",fe,_(e.$t("message.spaceLabel"))+": "+_(p.rootSpace.title),1)):S("",!0),c("div",null,_(e.$t("message.people"))+": "+_(t.selectedReservation.people),1),c("div",null,_(e.$t("message.beds"))+": "+_(t.selectedReservation.beds),1)])):S("",!0),Te,l(u,{type:"danger",onClick:p.resetSale},{default:i(()=>[b("Reset Sale")]),_:1},8,["onClick"]),l(f,{"onSaleGroupSelect:groupSelected":p.groupSelected},null,8,["onSaleGroupSelect:groupSelected"]),o.selectedGroup?(n(),y(d,{key:1,selectedGroup:o.selectedGroup,"onSaleTypePicker:saleTypeSelected":p.saleTypeSelected},null,8,["selectedGroup","onSaleTypePicker:saleTypeSelected"])):S("",!0),be,l(T,{saleType:o.selectedSaleType,processSaleTypeResetKey:o.processSaleTypeResetKey,"onProcessSaleType:addSaleItem":p.addSaleItem},null,8,["saleType","processSaleTypeResetKey","onProcessSaleType:addSaleItem"]),o.saleItems.length>0?(n(),y(k,{key:2,saleItems:o.saleItems,"onSaleItems:removeAtIndex":p.removeItemAtIndex},null,8,["saleItems","onSaleItems:removeAtIndex"])):S("",!0),o.saleItems.length>0?(n(),y(P,{key:3,saleItems:o.saleItems,"onCompeleteSale:paymentTypeSelected":p.handlePaymentTypeSelected},null,8,["saleItems","onCompeleteSale:paymentTypeSelected"])):S("",!0)]),_:1}),l(D,{span:18},{default:i(()=>[l(F,{folioViewerReloadKey:o.folioViewerReloadKey,folioId:t.folioId},null,8,["folioViewerReloadKey","folioId"])]),_:1})]),_:1})])}const ve=g(_e,[["render",ge]]);export{ve as F};
