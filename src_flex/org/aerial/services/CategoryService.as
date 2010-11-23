package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.CategoryVO;

	public class CategoryService extends AbstractService
	{
		public function CategoryService()
		{
			super("CategoryService", "http://aerial-test/server.php", CategoryVO);
		}
	}
}