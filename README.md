# Custom field to customer, Prestashop 1.7.7

1. Put all content inside x `modules/ps_customerfields`.
2. Go to install module.
3. Clean cache in the admin area `Advanced Parameters / Performance`.

Warning: 
- this create/delete the field `sdi` in the `customer` table. 
- Prestashop copy the customer class in the [override folder](https://devdocs.prestashop.com/1.7/modules/concepts/overrides/#overriding-a-class).