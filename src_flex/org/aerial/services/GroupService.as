package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	
	import org.aerial.vo.GroupVO;
	import org.aerial.bootstrap.Aerial;

	public class GroupService extends AbstractService
	{
		public function GroupService()
		{
			super("GroupService", Aerial.SERVER_URL, GroupVO);
		}
	}
}