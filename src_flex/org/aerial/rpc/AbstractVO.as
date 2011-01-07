package org.aerial.rpc
{
	import flash.net.registerClassAlias;
	import flash.utils.IDataInput;
	import flash.utils.IDataOutput;
	import flash.utils.IExternalizable;
	import flash.utils.describeType;
	import flash.utils.getDefinitionByName;
	import flash.utils.getQualifiedClassName;


    public class AbstractVO implements IExternalizable
	{
		private var getPrivateProperty:Function;
		private var setPrivateProperty:Function;
		
		public function AbstractVO(aliasName:String, getProp:Function, setProp:Function)
		{
			getPrivateProperty = getProp;
			setPrivateProperty = setProp;
		}
		
		public function isUndefined(property:String):Boolean
		{
			try{
				var isUndef:Boolean = (getPrivateProperty("_" + property) === undefined);
			}catch(e:ReferenceError){
				throw e;
			}
			return isUndef;
		}
		
		public function isNull(property:String):Boolean
		{
			try{
				var _isNull:Boolean = (getPrivateProperty("_" + property) === null);
			}catch(e:ReferenceError){
				throw e;
			}
			return _isNull;
		}

        public function setNull(property:String):void
        {
			if(!this.hasOwnProperty(property))
            {
                var voType:String = getQualifiedClassName(this).replace("::",".");
			    var voClass:Class = getDefinitionByName(voType) as Class;
                throw new ArgumentError("No such property [" + property + "] in " + voClass);
            }
			
			setPrivateProperty("_" + property, null);
        }
		
		public function readExternal(input:IDataInput):void
		{	
			//We shouldn't have to set this as long as we don't return an IExternalizable from AMFPHP.
			//Otherwise, we'll need to loop through the input and set each property.
		}
		
		public function writeExternal(output:IDataOutput):void
		{
			//To preserve the "undefined" values, we'll just set the private vars.
			var voOutput:Object = new Object();
			
			var descr:XML = describeType(this);
			var props:XMLList = descr..accessor + descr..variable;
			
			for each(var prop:XML in props)
			{ 
				var propName:String = prop.@name.toString();
				try{
					var propValue:* = getPrivateProperty("_" + propName);
				}catch(e:Error){
					throw e;
				}
				voOutput[propName] = propValue;				
			}

			output.writeObject(voOutput);
		}
	}

}