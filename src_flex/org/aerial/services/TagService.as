package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.TagVO;

	public class TagService extends AbstractService
	{
		public function TagService()
		{
			super("TagService", "http://aerial-test/server.php", TagVO);
		}
	}
}