import Resource from '@/api/resource';
import request from '@/utils/requests';

class SettingBlock extends Resource {
  constructor() {
    super('settings/blocks');
  }

  getTypes() {
    return request({
      url: '/' + this.uri + '/get-types',
      method: 'get',
    });
  }

  updateSort(id, data) {
    return request({
      url: '/' + this.uri + '/' + id + '/update-sort',
      method: 'put',
      data: data,
    });
  }

  updateListSort(data) {
    return request({
      url: '/' + this.uri + '/update-list-sort',
      method: 'put',
      data: {data: data},
    });
  }

  getItems(id) {
    return request({
      url: '/' + this.uri + '/' + id + '/items',
      method: 'get',
    });
  }

  storeItem(blockId, data) {
    return request({
      url: '/' + this.uri + '/' + blockId + '/items',
      method: 'post',
      data: data,
    });
  }

  updateItem(blockId, id, data) {
    return request({
      url: '/' + this.uri + '/' + blockId + '/items/' + id,
      method: 'put',
      data: data,
    });
  }

  deleteItem(blockId, id) {
    return request({
      url: '/' + this.uri + '/' + blockId + '/items/' + id,
      method: 'delete',
    });
  }

  updateSortItem(blockId, data) {
    return request({
      url: '/' + this.uri + '/' + blockId + '/items/update-sort',
      method: 'put',
      data: data,
    });
  }
}

export { SettingBlock as default };
