package org.aerialframework.pagination
{
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	
	import mx.collections.CursorBookmark;
	import mx.collections.ICollectionView;
	import mx.collections.IViewCursor;
	
	public class AsyncCollectionCursor extends EventDispatcher implements IViewCursor
	{
		private var _view:AsyncCollection;
		private var _index:int;
		
		public function AsyncCollectionCursor( view : AsyncCollection )
		{
			_view = view;
			_index = -1;
		}
		
		public function get afterLast():Boolean
		{
			return _index >= view.length;
		}
		
		public function get beforeFirst():Boolean
		{
			return _index < 0;
		}
		
		public function get bookmark():CursorBookmark
		{
			return new CursorBookmark( _index );
		}
		
		public function get current():Object
		{
			return _view.getItemAt( _index );
		}
		
		public function get view():ICollectionView
		{
			return _view;
		}
		
		public function findAny(values:Object):Boolean
		{
			trace( "findAny not implemented" );
			
			return false;
		}
		
		
		
		public function findFirst(values:Object):Boolean
		{
			trace( "findFirst not implemented" );
			return false;
		}
		
		public function findLast(values:Object):Boolean
		{
			trace( "findLast not implemented" );
			return false;
		}
		
		public function insert(item:Object):void
		{
			trace( "insert not implemented " );
		}
		
		public function moveNext():Boolean
		{
			_index++;
			return !afterLast;
		}
		
		public function movePrevious():Boolean
		{
			_index--;
			return !beforeFirst;
		}
		
		public function remove():Object
		{
			trace( "remove not implemented " );
			return null;
		}
		
		public function seek(bookmark:CursorBookmark, offset:int=0, prefetch:int=0):void
		{
			switch( bookmark )
			{
				case CursorBookmark.FIRST:
					_index = offset;
					break;
				case CursorBookmark.LAST:
					_index = _view.length - 1 + offset;
					break;
				case CursorBookmark.CURRENT:
					_index += offset;
					break;
				default:
					_index = bookmark.value + offset;
					break;
			}
		}
	}
}