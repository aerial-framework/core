package org.aerialframework.pagination
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	import mx.collections.ArrayList;
	import mx.collections.ICollectionView;
	import mx.collections.IList;
	import mx.collections.ISort;
	import mx.collections.IViewCursor;
	import mx.collections.Sort;
	import mx.events.CollectionEvent;
	import mx.events.CollectionEventKind;
	import mx.resources.IResourceManager;
	import mx.resources.ResourceManager;
	import mx.rpc.AsyncResponder;
	import mx.rpc.AsyncToken;
	import mx.rpc.Responder;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	
	import org.aerialframework.rpc.operation.Operation;
	
	public class AsyncCollection extends EventDispatcher implements ICollectionView, IList
	{
		private var _allowParallelRequests:Boolean;
		private var _length:int;
		private var _sort:Sort;
		private var _list:ArrayList; 
		private var _queue:Object; 
		private var _operation:Operation;
		private var _resourceManager:IResourceManager = ResourceManager.getInstance();
		private var _pendingItem:AsyncPendingItem;
		private var _failedItem:AsyncFailedItem;
		//private var _updateEvent:CollectionEvent;
		
		public var pagePrefetch:uint = 0;
		public var pagePostfetch:uint = 0;
		public var pageSize:uint = 10;
		
		public function AsyncCollection()
		{
			super();
			_queue = new Array();
			_pendingItem = new AsyncPendingItem();
			_failedItem = new AsyncFailedItem();
			_list = new ArrayList();
			
			_list.addEventListener(CollectionEvent.COLLECTION_CHANGE, dispatchEventHandler);
			//_updateEvent = new CollectionEvent(CollectionEvent.COLLECTION_CHANGE, false, false, CollectionEventKind.ADD);
			
			addEventListener("operationChange", operationChangeHandler);
		}
		
		protected function dispatchEventHandler(event:CollectionEvent):void
		{
			dispatchEvent(event);	
		}
		
		protected function operationChangeHandler(event:Event):void
		{
			//Can't use callback functions since there's no way to change pointers in AS3 
			//and we're re-using this operation in different contexts.
			var asyncToken:AsyncToken = _operation.count();
			asyncToken.addResponder(new Responder(countResultHandler, null));
		}
		
		public function get operation():Operation
		{
			return _operation;
		}
		
		[Bindable(event="operationChange")]
		public function set operation(value:Operation):void
		{
			if( _operation !== value)
			{
				_operation = value;
				dispatchEvent(new Event("operationChange"));
			}
		}
		
		private function countResultHandler(event:ResultEvent):void
		{
			if(event.result)
				length = uint(event.result);
		}
		
		public function get list():ArrayList
		{
			return _list;
		}
		
		public function get length():int
		{
			return _length;
		}
		
		public function set length(value:int):void
		{
			_length = value;
			
			_list.source = new Array(_length);
			//dispatchEvent(_updateEvent);
		}
		
		public function get filterFunction():Function
		{
			return null;
		}
		
		public function getItemAt(index:int, prefetch:int = 0):Object 
		{
			if (index < 0 || index >= length)
			{
				var message:String = _resourceManager.getString("collections", "outOfBounds", [ index ]);
				throw new RangeError(message);
			}
			
			if (!list.source.hasOwnProperty(index)) 
			{
				var requestedPage:uint = Math.floor(index / pageSize);
				
				if(!_queue[requestedPage])
				{
					//Can't use callback functions since there's no way to change pointers in AS3
					//and we're re-using this operation in different contexts.
					var asyncToken:AsyncToken = _operation.execute(pageSize, pageSize * (requestedPage));
					asyncToken.addResponder(new AsyncResponder(resultHandler, faultHandler, requestedPage));
					_queue[requestedPage] = "Loading";
				}
				
				return _pendingItem;
			}
			else
			{
				return _list.getItemAt(index);
			}
		}
		
		private function resultHandler(event:ResultEvent, page:int):void
		{
			if(event.result)
			{
				var startIndex:int = pageSize*(page);
				var resultList:IList = event.result as IList;
				
				for (var i:uint = 0; i < event.result.length; i++)
				{
					list.setItemAt(event.result[i], startIndex + i);
				}
				
				delete(_queue[page]);
				//dispatchEvent(_updateEvent);
			}
		}
		
		private function faultHandler(event:FaultEvent, page:int):void
		{
			trace(event.fault.faultString);
		}
		
		
		public function set filterFunction(value:Function):void
		{
			trace( "set filterFunction not implemented" );
		}
		
		public function get sort():ISort
		{
			return _sort;
		}
		
		public function set sort(value:ISort):void
		{
			_sort = value as Sort;
		}
		
		public function createCursor():IViewCursor
		{
			return new AsyncCollectionCursor( this );
		}
		
		public function contains(item:Object):Boolean
		{
			return false;
		}
		
		public function disableAutoUpdate():void
		{
			trace( "disableAutoUpdate not implemented" );
		}
		
		public function enableAutoUpdate():void
		{
			trace( "enableAutoUpdate not implemented" );
		}
		
		public function itemUpdated(item:Object, property:Object=null, oldValue:Object=null, newValue:Object=null):void
		{
			trace( "itemUpdated not implemented" );
		}
		
		public function refresh():Boolean
		{
			_list = new ArrayList();
			_queue = new Array();
			dispatchEvent( new CollectionEvent( CollectionEvent.COLLECTION_CHANGE, false, false, CollectionEventKind.REFRESH ));			
			return true;
		}
		
		public function addItem(item:Object):void
		{
			addItemAt(item, length);
		}
		
		public function addItemAt(item:Object, index:int):void
		{
			list.addItemAt(item, index);
		}
		
		public function getItemIndex(item:Object):int
		{
			return list.getItemIndex(item);
		}
		
		public function removeAll():void
		{
			list.removeAll();
		}
		
		public function removeItemAt(index:int):Object
		{
			return list.removeItemAt(index);
		}
		
		public function setItemAt(item:Object, index:int):Object
		{
			return list.setItemAt(item, index);
		}
		
		public function toArray():Array
		{
			return list.toArray();
		}
		
	}
}
