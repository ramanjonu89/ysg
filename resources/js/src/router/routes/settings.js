export default [
  {
    path: '/settings',
    redirect: '/',
    meta: {
      label: 'Settings'
    },
    component: {
      render(c) {
        return c('router-view')
      }
    },
    children: [
      {
        path: 'feature-products',
        redirect: 'feature-products/list',
        pageTitle: 'Setting - Feature Products',
        meta: {
          pageTitle: 'Setting - Feature Products',
          label: 'Feature Products',
          resource: 'settings.feature-products',
          action: 'manage',
        },
        name: 'settings.feature-products',
        component: {
          render(c) {
            return c('router-view')
          }
        },
        children: [
          {
            path: 'list',
            meta: {
              pageTitle: 'Feature Products',
              label: 'List',
              breadcrumb: [
                {
                  text: 'Settings',
                  active: true,
                },
                {
                  text: 'Feature Products',
                  active: true,
                },
                {
                  text: 'List',
                  active: true,
                },
              ],
            },
            name: 'settings.feature-products.list',
            component: () => import('@/views/settings/feature-product/List.vue'),
          },
          // {
          //   path: ':id',
          //   meta: {
          //     pageTitle: 'Slider Details',
          //     label: 'Slider Details'
          //   },
          //   name: 'SliderItem',
          //   component: SliderItem,
          // },
        ]
      },
      {
        path: 'blocks',
        redirect: 'blocks/list',
        meta: {
          pageTitle: 'Setting - Blocks',
          label: 'Blocks',
          resource: 'settings.blocks',
          action: 'manage',
        },
        name: 'settings.blocks',
        component: {
          render(c) {
            return c('router-view')
          }
        },
        children: [
          {
            path: 'list',
            meta: {
              pageTitle: 'Infocubes',
              label: 'List',
              breadcrumb: [
                {
                  text: 'Settings',
                },
                {
                  text: 'Infocubes',
                },
                {
                  text: 'List',
                  active: true,
                },
              ],
              buttons: [
                {
                  text: 'Add Infocube',
                  linkTo: 'settings.blocks.add'
                }
              ]
            },
            name: 'settings.blocks.list',
            component: () => import('@/views/settings/block/List.vue'),
          },
          {
            path: 'add',
            meta: {
              pageTitle: 'Infocubes',
              label: 'Add',
              breadcrumb: [
                {
                  text: 'Settings',
                },
                {
                  text: 'Infocubes',
                  to: { name: 'settings.blocks.list' }
                },
                {
                  text: 'Add',
                  active: true,
                },
              ],
            },
            name: 'settings.blocks.add',
            component: () => import('@/views/settings/block/Add.vue'),
          },
          {
            path: ':id',
            meta: {
              pageTitle: 'Infocubes',
              label: 'Add',
              breadcrumb: [
                {
                  text: 'Settings',
                },
                {
                  text: 'Infocubes',
                  to: { name: 'settings.blocks.list' }
                },
                {
                  text: 'Edit',
                  active: true,
                },
              ],
            },
            name: 'settings.blocks.edit',
            component: () => import('@/views/settings/block/Edit.vue'),
          },
        ]
      },
    ]
  },
]
