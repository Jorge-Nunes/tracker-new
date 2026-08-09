import createCrudModule from './factory';

export default createCrudModule({
    listKey: 'attributesList',
    singular: 'Attribute',
    api: { load: 'getComputedAttributes', create: 'createComputedAttribute', update: 'updateComputedAttribute', remove: 'deleteComputedAttribute' },
    mut: { set: 'setAttributes', add: 'addAttribute', update: 'updateAttribute', remove: 'removeAttribute' },
    act: { load: 'load', save: 'save', remove: 'delete' },
    guardRemove: true,
});
