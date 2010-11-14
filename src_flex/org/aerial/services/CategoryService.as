package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.CategoryVO;
	import org.aerial.config.Config;

	public class CategoryService extends AbstractService
	{
		public function CategoryService()
		{
			super("CategoryService", Config.SERVER_URL, CategoryVO);
		}
	}
}