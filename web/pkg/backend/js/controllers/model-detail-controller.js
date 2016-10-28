(function(){
  'use strict';

  angular
    .module('BackendApp')
    .controller('ModelDetailController', ModelDetailController);

  ModelDetailController.$inject = ['$scope', '$mdDialog', 'DataShareService', 'APIService'];
  function ModelDetailController($scope, $mdDialog, DataShareService, APIService){
    console.log('ModelDetailController');
    
    var vm = this;
    
    vm.fields       = [];
    vm.num_pag      = 10;
    vm.pag          = 1;
    vm.pags         = [];
    vm.records      = [];
    vm.refs         = {};
    vm.num_records  = 0;
    vm.selectedList = [];
    vm.order_by     = null;
    vm.order_sent   = null;

    vm.showRecords   = showRecords;
    vm.updateNumPag  = updateNumPag;
    vm.updateRecords = updateRecords;
    vm.deleteRecord  = deleteRecord;
    vm.selectRow     = selectRow;
    vm.deleteBatch   = deleteBatch;
    vm.orderBy       = orderBy;
    vm.add           = add;
    vm.editRecord    = editRecord;
    
    $scope.select_all = false;
    
    $scope.$watch('selected', function (newValue, oldValue, scope) {
      loadData();
    });
    
    $scope.$watch('select_all', function (newValue, oldValue, scope) {
      selectAll();
    });
    
    function loadData(){
      $scope.select_all = false;
      vm.fields      = [];
      vm.records     = [];
      vm.refs        = {};
      vm.num_records = 0;

      for (var key in $scope.selected.fields){
        $scope.selected.fields[key].name = key;
        vm.fields.push($scope.selected.fields[key]);

        if ($scope.selected.fields[key].ref!=''){
          vm.refs[key] = getRefInfo($scope.selected.fields[key].ref);
          vm.refs[key].list = {};
        }
      }

      vm.width = roundTo( (80 / vm.fields.length), 5);

      updateRecords();
    }
    
    function getRefInfo(ref){
      var ref_data = ref.split('.');
      var id = ref_data[1];
      var ref_table = ref_data[0].split('(');
      var model = ref_table[0];
      var table = ref_table[1].replace(')','');
      
      return {model: model, table: table, id: id};
    }

    function updateNumPag(){
      vm.pag = 1;
      updateRecords();
    }

    function updateRecords(){
      APIService.GetRecords($scope.selected.tablename, vm.num_pag, vm.pag, vm.order_by, vm.order_sent, getRecordsSuccess);
    }

    function getRecordsSuccess(response){
      if (Object.keys(vm.refs).length!=0){
        for(var i in response.list){
          for (var key in vm.refs){
            vm.refs[key].list['item_'+response.list[i][key]] = '';
          }
        }

        APIService.GetRefs(vm.refs,response,refsSuccess);
      }
      else {
        prepareRecords(response);
      }
    }

    function prepareRecords(data){
      vm.records     = data.list;
      vm.num_records = data.num;

      DataShareService.SetGlobal($scope.selected.tablename+'_num',     vm.num_records);
      DataShareService.SetGlobal($scope.selected.tablename+'_records', vm.records);


      showRecords();
    }
    
    function refsSuccess(data,response){
      vm.refs = response.refs;
      prepareRecords(data);
    }

    function showRecords(){
      vm.pags = [];
      for (var i=1;i<=Math.ceil(vm.num_records/vm.num_pag); i++){
        vm.pags.push(i);
      }
    }
    
    function selectAll(){
      for (var i in vm.records){
        i = parseInt(i);
        vm.records[i].selected = $scope.select_all;
        if (vm.records[i].selected){
          if (vm.selectedList.indexOf(i)==-1) {
            vm.selectedList.push(i);
          }
        }
        else{
          vm.selectedList.splice(vm.selectedList.indexOf(i),1);
        }
      }
    }

    function selectRow(ind){
      if (vm.records[ind].selected){
        vm.selectedList.push(ind);
      }
      else{
        vm.selectedList.splice(vm.selectedList.indexOf(ind),1);
      }
    }

    function deleteRecord(ev,ind){
      var confirm = $mdDialog.confirm()
        .title('Borrar registro')
        .textContent('¿Estás seguro de querer borrar este registro? Esta acción es irreversible.')
        .ariaLabel('Borrar registros')
        .targetEvent(ev)
        .ok('Continuar')
        .cancel('Cancelar');

      $mdDialog.show(confirm).then(function() {
        APIService.DeleteRecord($scope.selected.name, $scope.selected.tablename,vm.records[ind],ind,deleteSuccess);
      }, function() {});
    }

    function deleteSuccess(ind){
      vm.records.splice(ind,1);
      if (vm.records.length==0){
        vm.pag--;
        if (vm.pag==0){
          vm.pag = 1;
        }
      }
      updateRecords();
    }

    function deleteBatch(ev){
      var confirm = $mdDialog.confirm()
        .title('Borrar registros')
        .textContent('¿Estás seguro de querer borrar los '+vm.selectedList.length+' registros seleccionados? Esta acción es irreversible.')
        .ariaLabel('Borrar registros')
        .targetEvent(ev)
        .ok('Continuar')
        .cancel('Cancelar');

      $mdDialog.show(confirm).then(function() {
        var list = [];
        for (var i in vm.selectedList){
          list.push( vm.records[vm.selectedList[i]] );
        }
        APIService.DeleteRecords($scope.selected.name, $scope.selected.tablename, list, deleteBatchSuccess);
      }, function() {});
    }
    
    function deleteBatchSuccess() {
      var list = vm.selectedList.sort(function(a, b){return b-a});
      for (var i in list){
        vm.records.splice(list[i],1);
      }
      if (vm.records.length==0){
        vm.pag--;
        if (vm.pag==0){
          vm.pag = 1;
        }
      }
      updateRecords();
    }
    
    function orderBy(field){
      vm.order_by = field;
      vm.order_sent = (vm.order_sent=='asc')?'desc':'asc';
      updateRecords();
    }
    
    function add(ev){
      $mdDialog.show({
        controller: AddEditController,
        controllerAs: 'vm',
        templateUrl: '/pkg/backend/partials/add-edit.html',
        parent: angular.element(document.body),
        targetEvent: ev,
        clickOutsideToClose:true,
        locals: {
          isnew: true,
          model: $scope.selected,
          record: null
        }
      })
      .then(function(record) {
        APIService.AddEditRecord($scope.selected.tablename, record, updateRecords);
      }, function() {});
    }
    
    function AddEditController($scope, $mdDialog, isnew, model, record){
      var vm = this;
      if (isnew){
        vm.title = 'Añadir registro';
      }
      else{
        vm.title = 'Editar registro';
      }
      vm.model = model;
      vm.record = {
        fields: []
      }

      for(var i in model.fields){
        var item = {
          name: model.fields[i].name,
          value: (record===null)?'':record[i],
          disabled: false,
          type: model.fields[i].type
        };
        if (model.fields[i].type==1 && model.fields[i].incr){
          item.disabled = true;
        }
        if ([2,3,6].indexOf(model.fields[i].type)!=-1){
          if (record===null){
            item.value = new Date();
          }
          else{
            item.value = new Date(record[i]);
          }
        }
        
        vm.record.fields.push(item);
      }

      vm.close = close;
      vm.save  = save;
      
      function close() {
        $mdDialog.cancel();
      }
      function save() {
        $mdDialog.hide(vm.record);
      };
    }
    
    function editRecord(ev,ind){
      $mdDialog.show({
        controller: AddEditController,
        controllerAs: 'vm',
        templateUrl: '/pkg/backend/partials/add-edit.html',
        parent: angular.element(document.body),
        targetEvent: ev,
        clickOutsideToClose:true,
        locals: {
          isnew: false,
          model: $scope.selected,
          record: vm.records[ind]
        }
      })
      .then(function(record) {
        APIService.AddEditRecord($scope.selected.tablename, record, updateRecords);
      }, function() {});
    }
  }
})();