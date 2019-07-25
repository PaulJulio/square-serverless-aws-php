var InventoryClass = Backbone.Collection.extend({
    url: "inventory",
    render: function(){
        // todo: this is the wrong way to do templating, must fix
        var itemTemplate = _.template($('#tpl-menu-item').html());
        var pricingTemplate = _.template($('#tpl-menu-item-pricing').html());
        var compiled = '';
        var compiledPricing = '';
        this.forEach(function(item){
            compiledPricing = '';
            _.each(item.attributes.variations, function(variation){
                compiledPricing = compiledPricing + pricingTemplate(variation);
            });
            item.set('pricing', compiledPricing);
            compiled = compiled + itemTemplate(item.attributes);
        });
        $("#menuitems").html(compiled);
    }
});
var Inventory = new InventoryClass();

var OrderView = Backbone.View.extend({
    initialize: function() {
        this.listenTo(this.model, "change", this.render)
        this._template = _.template('<button type="button" class="btn btn-warning">Remove</button> <%-name%><span class="pull-right">$<%-price%></span>');
        this.render();
    },
    render: function() {
        this.$el.html(this._template(this.model.attributes));
    },
    events: {
        "click button.btn-warning": 'removeItem'
    },
    removeItem: function() {
        OrderCollection.remove(this.model);
        this.remove();
    }
});

var OrderModel = Backbone.Model.extend({
    initialize: function() {
        var myEl = $('<li class="list-group-item"></li>');
        myEl.appendTo('#cart');
        var myView = new OrderView({
            el: myEl,
            model: this
        });
    }
});
var OrderCollectionClass = Backbone.Collection.extend({
    model: OrderModel,
    total: function() {
        var total = 0;
        this.each(function(model){total = total + model.get('price');});
        return total;
    }
});
var OrderCollection = new OrderCollectionClass();

var TotalViewClass = Backbone.View.extend({
    initialize: function() {
        this.listenTo(OrderCollection, "update", this.render);
        this._template = _.template('<li class="list-group-item"><button id="purchase-button" type="button" class="btn btn-success">Purchase</button> <button type="button" class="btn btn-outline-primary"><%-count%> Items</button> <span class="pull-right">$<%-total%></span></li>');
    },
    render: function() {

        var values = {
            count: OrderCollection.length,
            total: OrderCollection.total()
        };
        this.$el.html(this._template(values));
    },
    events: {
        "click button.btn-success": 'purchase'
    },
    purchase: function() {
        console.log('start purchase process');
        $('#form-container').show();
        paymentForm.build();
        paymentForm.recalculateSize();
        $('#purchase-button').remove();
    }
});
var TotalView = new TotalViewClass({
    el: $('#cart-total')
});

Inventory.fetch({
    success(collection, resposne, options) {
        collection.render();
        $('#menuitems').on('click', 'button.add-item-to-cart', function(){
            var $this = $(this);
            OrderCollection.add({
                itemId: $this.data('item-id'),
                sku: $this.data('item-sku'),
                price: $this.data('item-price'),
                name: $this.data('item-name')
            });
        });
    }
});

function processOrder() {
    var nonce = $('#card-nonce').val();
    var payload = {
        nonce: nonce,
        items: []
    }
    $('#form-container').remove();
    OrderCollection.each(function(item){
        payload.items.push( item.attributes );
    });
    console.log('sending payload', payload);
    window.payload = payload;
    $('#cart-total').html('<li class="list-group-item"><button class="btn btn-outline-info" type="button">Processing Payment</button></li>');
    $.ajax({
        url: 'order/',
        method: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json',
        success: function(data) {
            var tpl = _.template('<li class="list-group-item"><button class="btn btn-success" type="button"><%-status%></button> Transaction ID: <%-id%></li>');
            $('#cart-total').html(tpl(data));
        },
        error: function(a,b,c) {
            $('#cart-total').html('<li class="list-group-item"><button class="btn btn-danger" type="button">Error</button> Please ask for help.</li>');
        }
    })
}
